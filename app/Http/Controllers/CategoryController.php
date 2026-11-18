<?php
namespace App\Http\Controllers;
use App\Models\Category; use Illuminate\Http\Request; use Illuminate\Support\Str;
class CategoryController extends Controller
{
 public function index(){ $categories=Category::with('parent')->orderBy('name')->paginate(20); return view('categories.index',compact('categories')); }
 public function store(Request $r){ $data=$r->validate(['name'=>'required|string|max:120','parent_id'=>'nullable|exists:categories,id']); $data['slug']=Str::slug($data['name']).'-'.Str::lower(Str::random(5)); Category::create($data); return back()->with('success','Category added.'); }
 public function update(Request $r,Category $category){$data=$r->validate(['name'=>'required|string|max:120','parent_id'=>'nullable|exists:categories,id']); $data['slug']=Str::slug($data['name']).'-'.$category->id; $category->update($data); return back()->with('success','Category updated.');}
 public function destroy(Category $category){if($category->products()->exists()||$category->children()->exists())return back()->withErrors(['category'=>'Category has products or subcategories.']); $category->delete(); return back()->with('success','Category deleted.');}
}
