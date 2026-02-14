<?php
 use App\Models\Farmercategory;
 use App\Models\Farmersubcategory;

 function user()
 {
 	$user = auth()->user();
 	return $user;
 }

 function categories()
 {
 	$categories = Farmercategory::latest()->get();
 	return $categories;
 }

 function subcategories()
 {
 	$subcategories = Farmersubcategory::latest()->get();
 	return $subcategories;
 }