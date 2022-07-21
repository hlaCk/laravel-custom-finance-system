<?php

namespace App\Http\Controllers\Api\Info;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\Abstracts\ModelIdsResource;
use App\Http\Resources\Api\Abstracts\SelectOptionsResource;
use App\Http\Resources\Api\Info\EntryCategoryResource;
use App\Models\Info\EntryCategory;
use Illuminate\Http\Request;

class EntryCategoryController extends ApiController
{
    public function options(Request $request, ?EntryCategory $entry_category = null)
    {
        return SelectOptionsResource::collection(EntryCategory::OnlyActive());
    }

    public function has_contractors(Request $request, EntryCategory $entry_category)
    {
        return $this->success($entry_category->only('has_contractor'));
    }

    public function only_has_contractors(Request $request)
    {
        return EntryCategoryResource::collection(
            EntryCategory::OnlyActive()->OnlyHasContractor()->get([ 'id', 'name', 'has_contractor' ])
        );
    }

    public function only_has_contractors_ids(Request $request)
    {
        return ModelIdsResource::collection(EntryCategory::OnlyActive()->OnlyHasContractor());
    }

    public function only_has_contractors_options(Request $request)
    {
        return SelectOptionsResource::collection(EntryCategory::OnlyActive()->OnlyHasContractor());
    }
}
