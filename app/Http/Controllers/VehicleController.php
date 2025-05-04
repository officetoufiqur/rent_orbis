<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Page;
use App\Models\Rating;
use App\Models\Seater;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller {

    public function index() {
        $brands    = Brand::active()->withCount('vehicles')->orderBy('name')->get();
        $seaters   = Seater::active()->withCount('vehicles')->orderBy('number')->get();
        $vehicles  = Vehicle::active()->latest()->paginate(getPaginate());
        $pageTitle = 'All Vehicles';

        $sections    = Page::where('tempname', activeTemplate())->where('slug', 'vehicle')->first();
        $seoContents = $sections->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;

        return view('Template::vehicles.index', compact('vehicles', 'pageTitle', 'brands', 'seaters', 'seoContents', 'seoImage', 'sections'));
    }

    public function details($id, $slug) {

        $pageTitle   = 'Vehicle Details';
        $vehicle     = Vehicle::active()->where('id', $id)->with('ratings')->withCount('ratings')->withAvg('ratings', 'rating')->firstOrFail();
        $rentalTerms = getContent('rental_terms.element', false, 1);
        $isRating    = Rating::where('user_id', auth()?->id())->where('id', $id)->count();
        return view('Template::vehicles.details', compact('vehicle', 'pageTitle', 'rentalTerms', 'isRating'));
    }

    public function filter(Request $request) {
        $pageTitle = 'Vehicle Search';
        $brands    = Brand::active()->withCount('vehicles')->orderBy('name')->get();
        $seaters   = Seater::active()->withCount('vehicles')->orderBy('number')->get();

        $vehicles = Vehicle::active()->searchable(['name', 'model'])->filter(['brand_id', 'seater_id', 'name', 'model']);

        if ($request->min_price) {
            $vehicles->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $vehicles->where('price', '<=', $request->max_price);
        }
        $vehicles = $vehicles->latest()->paginate(getPaginate())->withQueryString();

        return view('Template::vehicles.index', compact('vehicles', 'pageTitle', 'brands', 'seaters'));
    }

    public function brand($brand_id, $slug) {
        $brands  = Brand::active()->withCount('vehicles')->orderBy('name')->get();
        $seaters = Seater::active()->withCount('vehicles')->orderBy('number')->get();

        $vehicles  = Vehicle::active()->where('brand_id', $brand_id)->latest()->paginate(6);
        $pageTitle = 'Brand Vehicles';

        return view('Template::vehicles.index', compact('vehicles', 'pageTitle', 'brands', 'seaters'));
    }

    public function seater($seat_id) {
        $brands = Brand::active()->withCount('vehicles')->orderBy('name')->get();
        $seats  = Seater::active()->withCount('vehicles')->orderBy('number')->get();

        $vehicles  = Vehicle::active()->where('seater_id', $seat_id)->latest()->paginate(6);
        $pageTitle = 'Vehicles Seating';

        return view('Template::vehicles.index', compact('vehicles', 'pageTitle', 'brands', 'seats'));
    }
}
