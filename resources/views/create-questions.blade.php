<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">

    <title>Quiz Builder</title>
</head>

<body>
    <div class="container py-5">
        <h1>Quiz Builder</h1>

        <form action="{{ '/create-questions' }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="category1">Category 1</label>
                <select class="form-control" id="category1" name="category1">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="subcategory">Sub Category</label>
                <select class="form-control" id="subcategory" name="subcategory">
                    @foreach($sub_categories as $subcategory)
                        <option value="{{ $subcategory->id }}">{{ $subcategory->category_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="tag">Tag</label>
                <select class="form-control" id="tag" name="tag">
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->tag_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="json">JSON</label>
                <textarea class="form-control" id="json" name="json" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>
