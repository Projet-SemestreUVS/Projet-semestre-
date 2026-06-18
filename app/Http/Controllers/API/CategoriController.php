<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoriController extends Controller
{
    /**
     * Afficher toutes les catégories
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json($categories);
    }

    /**
     * Ajouter une catégorie
     */
    public function store(Request $request)
{

    $data = $request->validate([
        'nom'=>'required|string',
        'description'=>'nullable|string',
        'icone'=>'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
    ]);


    if($request->hasFile('icone')){

        $path = $request->file('icone')
              ->store('categories','public');

        $data['icone']=$path;

    }


    $category = Category::create($data);


    return response()->json($category);

}

    /**
     * Afficher une catégorie
     */
    public function show(string $id)
    {
        $categorie = Category::findOrFail($id);

        return response()->json($categorie);
    }

    /**
     * Modifier une catégorie
     */
  public function update(Request $request,$id)
{

$category = Category::findOrFail($id);


$data=$request->validate([
'nom'=>'required|string',
'description'=>'nullable|string',
'icone'=>'nullable|image|mimes:png,jpg,jpeg,webp|max:2048'
]);



if($request->hasFile('icone')){


$path=$request->file('icone')
->store('categories','public');


$data['icone']=$path;


}



$category->update($data);



return response()->json($category);


}

    /**
     * Supprimer une catégorie
     */
    public function destroy(string $id)
    {
        $categorie = Category::findOrFail($id);

        $categorie->delete();

        return response()->json([
            'message' => 'Categorie supprimée'
        ]);
    }
}