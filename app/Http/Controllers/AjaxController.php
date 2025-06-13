<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\Office;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AjaxController extends Controller
{
    public function state(Request $request)
    {
        $term = $request->term;

        $data = State::query()
            ->when($term, function ($e, $term) {
                $e->where('name', 'like', '%' . $term . '%');
            })
            ->where('status', true)
            ->select('id', 'name as label');

        if ($data->count() > 0) {
            return response()->json([
                'data'  => $data->get(),
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data'  => null,
            ]);
        }
    }

    public function city(Request $request)
    {
        $term = $request->term;
        $state = $request->state;

        $data = City::query()
            ->when($term, function ($e, $term) {
                $e->where('name', 'like', '%' . $term . '%');
            })
            ->where('status', true)
            ->when($state, function ($e, $state) {
                $e->where('state_id', $state);
            })
            ->select('id', 'name as label');

        if ($data->count() > 0) {
            return response()->json([
                'data'  => $data->get(),
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data'  => null,
            ]);
        }
    }

    public function district(Request $request)
    {
        $term = $request->term;
        $city = $request->city;

        $data = District::query()
            ->when($term, function ($e, $term) {
                $e->where('name', 'like', '%' . $term . '%');
            })
            ->where('status', true)
            ->where('city_id', $city)
            ->select('id', 'name as label');

        if ($data->count() > 0) {
            return response()->json([
                'data'  => $data->get(),
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data'  => null,
            ]);
        }
    }

    public function office(Request $request)
    {
        $term = $request->term;

        $data = Office::query()
            ->when($term, function ($e, $term) {
                $e->where('name', 'like', '%' . $term . '%');
            })
            ->where('status', true)
            ->select('id', 'name as label');

        if ($data->count() > 0) {
            return response()->json([
                'data'  => $data->get(),
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data'  => null,
            ]);
        }
    }

    public function category(Request $request)
    {
        $term = $request->term;

        $data = Category::query()
            ->when($term, function ($e, $term) {
                $e->where('name', 'like', '%' . $term . '%');
            })
            ->where('status', true)
            ->select('id', 'name as label');

        if ($data->count() > 0) {
            return response()->json([
                'data'  => $data->get(),
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data'  => [],
            ]);
        }
    }

    public function ganti_foto(Request $request)
    {
        if ($request->has('file')) {
            $file = $request->file;
            $path = $request->path;

            switch ($path) {

                case 'foto':
                    $size_gambar = 500;
                    break;

                case 'banner':
                    $size_gambar = 1024;
                    break;

                default:
                    $size_gambar = 400;
                    break;
            }

            $request->validate([
                'file' => 'required|image|max:7000'
            ]);

            $name = time() . rand(11111, 99999);
            $ext  = $file->getClientOriginalExtension();
            $foto = $name . '.' . $ext;

            $fullPath = $path . '/'  . $foto;

            $path = $file->getRealPath();
            $manager = new ImageManager(new Driver());
            $thum = $manager->read($path)->scale(width: $size_gambar);

            $path = Storage::put($fullPath, $thum->encode());

            return response()->json([
                'file' => $fullPath,
            ]);
        }
    }
}
