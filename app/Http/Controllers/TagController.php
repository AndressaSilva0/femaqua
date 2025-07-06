<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Tags",
 *     description="Gerenciamento de tags"
 * )
 */
class TagController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tags",
     *     summary="Listar todas as tags",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tags",
     *         @OA\JsonContent(type="array", @OA\Items(type="object", @OA\Property(property="nome", type="string")))
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Tag::all());
    }

    /**
     * @OA\Post(
     *     path="/api/tags",
     *     summary="Criar nova tag",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="api")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tag criada com sucesso"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     */
    public function store(Request $request)
    {
        $request->validate(['nome' => 'required|unique:tags']);

        $tag = Tag::create([
            'nome' => $request->nome
        ]);

        return response()->json($tag, 201);
    }
}
