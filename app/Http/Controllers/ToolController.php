<?php

/**
 * @OA\Tag(
 *     name="Tools",
 *     description="Gerenciamento de ferramentas"
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
     *     summary="Listar ferramentas ou filtrar por tag",
     *     tags={"Tools"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Nome da tag para filtrar ferramentas",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ferramentas"
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
     *     tags={"Tools"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","link","description","tags"},
     *             @OA\Property(property="title", type="string", example="Notion"),
     *             @OA\Property(property="link", type="string", example="https://notion.so"),
     *             @OA\Property(property="description", type="string", example="All in one workspace"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string", example="organization")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Ferramenta criada com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação")
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
     *     summary="Deletar ferramenta",
     *     tags={"Tools"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ferramenta deletada com sucesso"),
     *     @OA\Response(response=403, description="Acesso negado (não é admin)"),
     *     @OA\Response(response=404, description="Ferramenta não encontrada")
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
