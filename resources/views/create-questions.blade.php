@extends('layouts.initial-app')

@section('content')

@if (session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

<form id="quiz-builder-form" action="/create-questions" method="POST">
    @csrf

    <div class="form-group">
        <label for="category1">Category 1</label>
        <select class="form-control" id="category_id" name="category_id">
            <option value="">--- Select Category ---</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="subcategory">Sub Category</label>
        <select class="form-control" id="subcategory_id" name="subcategory_id">
        <option value="">--- Select Sub Category ---</option>
            @foreach($sub_categories as $subcategory)
            <option value="{{ $subcategory->id }}">{{ $subcategory->category_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="tag">Tag</label>
        <select class="form-control" id="tag_id" name="tag_id">
        <option value="">--- Select Tag ---</option>
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

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('quiz-builder-form');

        form.addEventListener('submit', function(event) {
            var category = document.getElementById('category_id').value;
            var subcategory = document.getElementById('subcategory_id').value;
            var tags = document.getElementById('tag_id').value;
            var quizText = document.getElementById('json').value;

            if (!category || !subcategory || !tags || !quizText) {
                event.preventDefault();
                alert('All fields must be filled out!');
                return false;
            }

            var confirmMessage = 'Please confirm:\n' +
                'Category: ' + category + '\n' +
                'Subcategory: ' + subcategory + '\n' +
                'Tags: ' + tags + '\n' +
                'Quiz Text: ' + quizText;

            if (!confirm(confirmMessage)) {
                event.preventDefault();
                return false;
            }
        });
    });
</script>
@endsection
