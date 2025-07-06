<?php

/**
 * @OA\Tag(
 *     name="Tools",
 *     description="Gerenciamento de ferramentas"
 * )
 *
 * @OA\Info(
 *     title="API FEMAQUA",
 *     version="1.0",
 *     description="Documentação da API de gerenciamento de ferramentas"
 * )
 */

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ToolController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tools",
     *     summary="Listar todas as ferramentas ou filtrar por tag",
     *     description="Retorna todas as ferramentas cadastradas. Pode-se filtrar por nome da tag usando o parâmetro 'tag'.",
     *     tags={"Tools"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         required=false,
     *         description="Nome da tag para filtrar as ferramentas (ex: 'organization')",
     *         @OA\Schema(type="string", example="organization")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ferramentas retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Notion"),
     *                 @OA\Property(property="link", type="string", format="url", example="https://notion.so"),
     *                 @OA\Property(property="description", type="string", example="All in one workspace"),
     *                 @OA\Property(
     *                     property="tags",
     *                     type="array",
     *                     @OA\Items(type="string", example="organization")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao buscar ferramentas"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = Tool::with('tags');

            if ($request->has('tag')) {
                $tag = $request->query('tag');
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('nome', $tag);
                });
            }

            $tools = $query->get()->map(function ($tool) {
                return [
                    'id' => $tool->id,
                    'title' => $tool->title,
                    'link' => $tool->link,
                    'description' => $tool->description,
                    'tags' => $tool->tags->pluck('nome')->toArray()
                ];
            });

            return response()->json($tools, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao buscar ferramentas.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/tools",
     *     summary="Cadastrar nova ferramenta",
     *     description="Cadastra uma nova ferramenta e associa com tags. Todos os campos são obrigatórios. O usuário precisa estar autenticado.",
     *     tags={"Tools"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","link","description","tags"},
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 example="Notion",
     *                 minLength=1,
     *                 maxLength=100,
     *                 description="Título da ferramenta (até 100 caracteres)"
     *             ),
     *             @OA\Property(
     *                 property="link",
     *                 type="string",
     *                 format="url",
     *                 example="https://notion.so",
     *                 maxLength=255,
     *                 description="URL da ferramenta"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 example="All in one workspace for notes, tasks, and wikis.",
     *                 minLength=1,
     *                 description="Descrição da ferramenta"
     *             ),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 minItems=1,
     *                 description="Lista de tags (mínimo 1)",
     *                 @OA\Items(
     *                     type="string",
     *                     maxLength=50,
     *                     example="organization"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ferramenta criada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ferramenta criada com sucesso."),
     *             @OA\Property(property="id", type="integer", example=5),
     *             @OA\Property(property="title", type="string", example="Notion"),
     *             @OA\Property(property="link", type="string", example="https://notion.so"),
     *             @OA\Property(property="description", type="string", example="Workspace completo"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string", example="organization")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro de validação."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:100',
                'link' => 'required|url|max:255',
                'description' => 'required|string',
                'tags' => 'required|array|min:1',
                'tags.*' => 'string|max:50'
            ]);

            $tool = Tool::create([
                'title' => $validated['title'],
                'link' => $validated['link'],
                'description' => $validated['description'],
                'usuario_id' => Auth::id()
            ]);

            $tags = [];
            foreach ($validated['tags'] as $tagName) {
                if (trim($tagName) === '') continue;
                $tag = Tag::firstOrCreate(['nome' => $tagName]);
                $tags[] = $tag->id;
            }

            $tool->tags()->attach($tags);

            return response()->json([
                'message' => 'Ferramenta criada com sucesso.',
                'title' => $tool->title,
                'link' => $tool->link,
                'description' => $tool->description,
                'tags' => $validated['tags'],
                'id' => $tool->id
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao cadastrar ferramenta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/tools/{id}",
     *     summary="Deletar uma ferramenta",
     *     description="Remove uma ferramenta existente. Apenas administradores podem realizar essa ação.",
     *     tags={"Tools"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da ferramenta a ser deletada",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ferramenta deletada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ferramenta deletada com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acesso negado (usuário não é admin)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Apenas administradores podem remover ferramentas.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ferramenta não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ferramenta não encontrada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno ao deletar ferramenta",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro ao deletar ferramenta."),
     *             @OA\Property(property="error", type="string", example="Mensagem detalhada do erro")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();

            if ($user->type !== 'admin') {
                return response()->json(['message' => 'Apenas administradores podem remover ferramentas.'], 403);
            }

            $tool = Tool::findOrFail($id);

            $tool->tags()->detach();
            $tool->delete();

            return response()->json(['message' => 'Ferramenta deletada com sucesso.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ferramenta não encontrada.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar ferramenta.', 'error' => $e->getMessage()], 500);
        }
    }
}
