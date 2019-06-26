<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Http\Resources\Api\V1\SectionCollection;
use App\Http\Resources\Api\V1\SectionItemCollection;

class SectionController extends Controller
{
    public function browse() {
        $sections = Section::query()->with('items')->get();
        return new SectionCollection($sections);
    }

    public function items(Request $request, $sectionId) {
        $section = Section::query()->with('items')->find($sectionId);
        if (!empty($section))
            return new SectionItemCollection($section->items);
    }
}
