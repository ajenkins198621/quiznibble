@extends('layouts.initial-app')

@section('content')

@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

<form action="/create-questions" method="POST">
    @csrf

    <div class="form-group">
        <label for="category1">Category 1</label>
        <select class="form-control" id="category_id" name="category_id">
            @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="subcategory">Sub Category</label>
        <select class="form-control" id="subcategory_id" name="subcategory_id">
            @foreach($sub_categories as $subcategory)
            <option value="{{ $subcategory->id }}">{{ $subcategory->category_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="tag">Tag</label>
        <select class="form-control" id="tag_id" name="tag_id">
            @foreach($tags as $tag)
            <option value="{{ $tag->id }}">{{ $tag->tag_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="json">JSON</label>
        <textarea class="form-control" id="json" name="json" rows="5"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection
