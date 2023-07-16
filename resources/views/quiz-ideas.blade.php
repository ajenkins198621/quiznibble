@extends('layouts.initial-app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add Quiz Idea</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" action="/create-quiz-idea">
                        @csrf
                        <div class="form-group">
                            <label for="idea">Quiz Idea:</label>
                            <textarea class="form-control" id="idea" name="idea" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">Quiz Ideas</div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Idea</th>
                                    <th scope="col">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quizIdeas as $quizIdea)
                                    <tr>
                                        <td>{{ $quizIdea->idea }}</td>
                                        <td>
                                            <form method="POST" action="/delete-quiz-idea">
                                                @csrf
                                                @method('DELETE')
                                                <input name='quiz_idea_id' type='hidden' value='{{ $quizIdea->id }}'>
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
