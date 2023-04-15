<?php

namespace Modules\Sliders\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Modules\Sliders\Entities\Slider;

class SlidersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (\request()->session()->has('brand_id')){
            $items = Slider::where('brand_id', \request()->session()->get('brand_id'))->get();
        }elseif (Auth::user()->brand_id) {
            $items = Slider::where('brand_id', Auth::user()->brand_id)->get();
        }else{
            $items = Slider::all();
        }

        return view('sliders::index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('sliders::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'lang' => 'required',
            'brand_id' => 'required',
        ]);
        try {
            $slider = Slider::create([
                'lang' => $request->lang,
                'brand_id' => $request->brand_id,
                'title' => $request->title,
                'sub_title' => $request->sub_title,
                'btn_text' => $request->btn_text,
                'btn_link' => $request->btn_link,
                'background' => (isset($request->background)?file_store($request->background, 'assets/uploads/photos/sliders_background/','photo_'):null),
                'image' => (isset($request->image)?file_store($request->image, 'assets/uploads/photos/sliders_images/','photo_'):null)
            ]);

            return redirect()->route('sliders.index')->with('flash_message', 'با موفقیت ثبت شد');
        }catch (\Exception $e){
            return redirect()->back()->withInput()->with('err_message', 'خطایی رخ داده است، لطفا مجددا تلاش نمایید');
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('sliders::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Slider $slider)
    {
        return view('sliders::edit', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, Slider $slider)
    {
        try {
            if ($request->lang) {
                $slider->lang = $request->lang;
            }
            if ($request->brand_id) {
                $slider->brand_id = $request->brand_id;
            }
            $slider->title = $request->title;
            $slider->sub_title = $request->sub_title;
            $slider->btn_text = $request->btn_text;
            $slider->btn_link = $request->btn_link;

            if (isset($request->background)){
                if ($slider->background){
                    File::delete($slider->background);
                }

                $slider->background = file_store($request->background, 'assets/uploads/photos/sliders_background/','photo_');
            }

            if (isset($request->image)){
                if ($slider->image){
                    File::delete($slider->image);
                }

                $slider->image = file_store($request->image, 'assets/uploads/photos/sliders_images/','photo_');
            }

            $slider->save();

            return redirect()->route('sliders.index')->with('flash_message', 'با موفقیت بروزرسانی شد');
        }catch (\Exception $e){
            return redirect()->back()->withInput()->with('err_message', 'خطایی رخ داده است، لطفا مجددا تلاش نمایید');
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Slider $slider)
    {
        try {
            $slider->delete();

            return redirect()->back()->with('flash_message', 'با موفقیت حذف شد');
        }catch (\Exception $e){
            return redirect()->back()->with('err_message', 'خطایی رخ داده است، لطفا مجددا تلاش نمایید');
        }
    }
}
