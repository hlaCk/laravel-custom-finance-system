<?php

use Illuminate\Http\Middleware\CheckResponseForModifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/nova-quick-search-options', 'NovaQuickSearchController@getOptions')->name('nova-quick-search.options');

// Scripts & Styles...
Route::get('/scripts/{script}', 'ScriptController@show')->middleware(CheckResponseForModifications::class)->name('script');
Route::get('/styles/{style}', 'StyleController@show')->middleware(CheckResponseForModifications::class)->name('style');

// Global Search...
Route::get('/search', 'SearchController@index')->name('search');

// Fields...
Route::get('/{resource}/field/{field}', 'FieldController@show')->name('field-show');
Route::post('/{resource}/trix-attachment/{field}', 'TrixAttachmentController@store');
Route::delete('/{resource}/trix-attachment/{field}', 'TrixAttachmentController@destroyAttachment');
Route::delete('/{resource}/trix-attachment/{field}/{draftId}', 'TrixAttachmentController@destroyPending');
Route::get('/{resource}/creation-fields', 'CreationFieldController@index')->name('creation-fields');
Route::get('/{resource}/{resourceId}/update-fields', 'UpdateFieldController@index')->name('update-fields');
Route::get('/{resource}/{resourceId}/creation-pivot-fields/{relatedResource}', 'CreationPivotFieldController@index')->name('creation-pivot-fields');
Route::get('/{resource}/{resourceId}/update-pivot-fields/{relatedResource}/{relatedResourceId}', 'UpdatePivotFieldController@index')->name('update-pivot-fields');
Route::get('/{resource}/{resourceId}/download/{field}', 'FieldDownloadController@show')->name('download-field');
Route::delete('/{resource}/{resourceId}/field/{field}', 'FieldDestroyController@handle')->name('field-handle');
Route::delete('/{resource}/{resourceId}/{relatedResource}/{relatedResourceId}/field/{field}', 'PivotFieldDestroyController@handle')->name('pivot-field-destroy');

// Dashboards...
Route::get('/dashboards/{dashboard}', 'DashboardController@index')->name('dashboard-index');
Route::get('/dashboards/cards/{dashboard}', 'DashboardCardController@index')->name('dashboard-cards');

// Actions...
Route::get('/{resource}/actions', 'ActionController@index')->name('actions');
Route::post('/{resource}/action', 'ActionController@store')->name('action-store');

// Filters...
Route::get('/{resource}/filters', 'FilterController@index')->name('filters');

// Lenses...
Route::get('/{resource}/lenses', 'LensController@index');
Route::get('/{resource}/lens/{lens}', 'LensController@show');
Route::get('/{resource}/lens/{lens}/count', 'LensResourceCountController@show');
Route::delete('/{resource}/lens/{lens}', 'LensResourceDestroyController@handle');
Route::delete('/{resource}/lens/{lens}/force', 'LensResourceForceDeleteController@handle');
Route::put('/{resource}/lens/{lens}/restore', 'LensResourceRestoreController@handle');
Route::get('/{resource}/lens/{lens}/actions', 'LensActionController@index');
Route::post('/{resource}/lens/{lens}/action', 'LensActionController@store');
Route::get('/{resource}/lens/{lens}/filters', 'LensFilterController@index');

// Cards / Metrics...
Route::get('/metrics', 'DashboardMetricController@index');
Route::get('/metrics/{metric}', 'DashboardMetricController@show');
Route::get('/{resource}/metrics', 'MetricController@index');
Route::get('/{resource}/metrics/{metric}', 'MetricController@show');
Route::get('/{resource}/{resourceId}/metrics/{metric}', 'DetailMetricController@show');

Route::get('/{resource}/lens/{lens}/metrics', 'LensMetricController@index');
Route::get('/{resource}/lens/{lens}/metrics/{metric}', 'LensMetricController@show');

Route::get('/cards', 'DashboardCardController@index')->name('dashboard-cards-index');
Route::get('/{resource}/cards', 'CardController@index')->name('resource-cards-index');
Route::get('/{resource}/lens/{lens}/cards', 'LensCardController@index')->name('resource-lens-cards-index');

// Authorization Information...
Route::get('/{resource}/relate-authorization', 'RelatableAuthorizationController@show');

// Soft Delete Information...
Route::get('/{resource}/soft-deletes', 'SoftDeleteStatusController@show');

// Resource Management...
Route::get('/{resource}', 'ResourceIndexController@handle')->name('resource-index');
Route::get('/{resource}/count', 'ResourceCountController@show')->name('resource-count');
Route::delete('/{resource}/detach', 'ResourceDetachController@handle')->name('resource-detach');
Route::put('/{resource}/restore', 'ResourceRestoreController@handle')->name('resource-restore');
Route::delete('/{resource}/force', 'ResourceForceDeleteController@handle')->name('resource-force-delete');
Route::get('/{resource}/{resourceId}', 'ResourceShowController@handle')->name('resource-show');
Route::post('/{resource}', 'ResourceStoreController@handle')->name('resource-store');
Route::put('/{resource}/{resourceId}', 'ResourceUpdateController@handle')->name('resource-update');
Route::delete('/{resource}', 'ResourceDestroyController@handle')->name('resource-destroy');

// Associatable Resources...
Route::get('/{resource}/associatable/{field}', 'AssociatableController@index')->name('associatable-index');
Route::get('/{resource}/{resourceId}/attachable/{field}', 'AttachableController@index')->name('attachable-index');
Route::get('/{resource}/morphable/{field}', 'MorphableController@index')->name('morphable-index');

// Resource Attachment...
Route::post('/{resource}/{resourceId}/attach/{relatedResource}', 'ResourceAttachController@handle');
Route::post('/{resource}/{resourceId}/update-attached/{relatedResource}/{relatedResourceId}', 'AttachedResourceUpdateController@handle');
Route::post('/{resource}/{resourceId}/attach-morphed/{relatedResource}', 'MorphedResourceAttachController@handle');
