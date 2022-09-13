<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Enums\StatusCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    protected $categoryService;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }


    public function index()
    {
        $categories = $this->categoryService->getListCategory();
        return response()->json($categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name'   => 'required|min:5|max:255|unique:categories'
        ],$this->getMessageValidate());

        $attributesCategory = $this->categoryService->getAttributeCategory($request);

        try{
            $category = $this->categoryService->create($attributesCategory);

            return response([
                'category' => $category
            ], StatusCode::OK);

        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json([
                'message' => StatusCode::INTERNAL_ERR,
            ], StatusCode::INTERNAL_ERR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = $this->categoryService->find($id);

        return response()->json($category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,
            [
                'name'  => 'required|min:5|max:255|unique:categories,name,'.$id
            ],$this->getMessageValidate()
        );

        $attributesCategory = $this->categoryService->getAttributeCategory($request);

        try{
            $category = $this->categoryService->update($id, $attributesCategory);

            return response([
                'category' => $category
            ], StatusCode::OK);

        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json([
                'message' => StatusCode::INTERNAL_ERR,
            ], StatusCode::INTERNAL_ERR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $this->categoryService->destroy($id);
            DB::commit();
            return response()->json(['result' => true], StatusCode::OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return  response()->json(['error' => true], StatusCode::INTERNAL_ERR);
        }

    }

    public function showButton()
    {
        return view('category.show-button');
    }

    public function showFormAdd()
    {
        return view('category.add');
    }

    public function showFormEdit($id)
    {
        return view('category.edit',compact('id'));
    }

    public function getMessageValidate()
    {
        return [
            'unique' => 'Please choose another name because this name already exists',
        ];
    }
}
