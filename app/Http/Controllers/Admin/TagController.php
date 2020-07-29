<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Http\Requests\TagRequest;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class TagController extends MainController
{

    public function store(TagRequest $tag)
    {
        try {
            firebaseGetReference('tags')->push($tag_name);
            return redirect()->route('admin.settings')->with('success', 'تم إضافة شكل المشروع: ' . $tag_name . ' بنجاح.');
        } catch (ApiException $e) {
            return redirect()->route('admin.settings')->with('error', 'حصلت مشكلة في إضافة شكل المشروع.');
        }
    }

    public function edit($tag_key)
    {

    }

    public function update(TagRequest $tag)
    {
        $tag_name = $tag->get('tag');
        $old_tag = $tag->get('old_tag_name');
        try {
            if ($tag->get('action') == 'store') {
                firebaseGetReference('tags')->push($tag_name);
                return redirect()->route('admin.settings')
                    ->with('success', 'تم إضافة شكل المشروع: ' . $tag_name . ' بنجاح.');
            } elseif ($tag->get('action') == 'update') {
                firebaseGetReference('tags')->update([$tag->get('tag_key') => $tag_name]);
                $groups = firebaseGetReference('groups')->getValue();
                foreach ($groups as $group_key => $group) {
                    if (isset($group['tags']) && $group['tags'] != null) {
                        foreach ($group['tags'] as $key => $tag) {
                            if ($tag == $old_tag) {
                                Arr::set($go_tags, $key, $tag_name);
                                Arr::forget($group['tags'], $key);
                                $go_tags = Arr::collapse([$go_tags, $group['tags']]);
                                firebaseGetReference('groups/' . $group_key . '/tags')->update($go_tags);
                                firebaseGetReference('groups')
                                    ->getChild($group_key)
                                    ->getChild('tags')
                                    ->update([$key => $tag_name]);
                            }
                        }
                    }
                }
                return redirect()->route('admin.settings')
                    ->with('success', 'تم تحديث شكل المشروع: ' . $tag_name . ' بنجاح.');
            } else {
                dd('hello');

            }
        } catch (ApiException $e) {
        }
    }

    public function destroy($tag_key)
    {
        try {
            $tag = firebaseGetReference('tags/' . $tag_key)->getValue();
            firebaseGetReference('tags/' . $tag_key)->remove();
            return redirect()->route('admin.settings')->with('success', 'تم حذف شكل المشروع ' . $tag);
        } catch (ApiException $e) {
        }

    }
}
