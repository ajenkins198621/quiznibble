@extends('layouts.initial-app')

@section('content')

<div class="row">
    <div class="col-md-12 mt-4">
        <div class="card">
            <div class="card-header">
                Questions
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Question</th>
                                <th scope="col">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $question)
                            <tr>
                                <th scope="row">{{ $question->id }}</th>
                                <td>
                                    {{ $question->question }}<br />
                                    <p style="margin-bottom:0">
                                        <small>
                                            Answers ({{count($question->answers)}})
                                            |
                                            {{ $question->category->parent->category_name }}
                                            >>
                                            {{ $question->category->category_name }}
                                            @foreach($question->tags as $tag)
                                            >>
                                            {{ $tag->tag_name }}
                                            @endforeach
                                    </p>
                                </td>
                                <td>{{ $question->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
