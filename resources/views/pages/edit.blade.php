<form action="{{ route('update', $movie->slug) }}" method="POST">
    @csrf
    <textarea id="description" name="description" rows="20" cols="150">{{ $movie->description }}</textarea>
    <br><br>
    <input type="submit" value="Submit">
</form>