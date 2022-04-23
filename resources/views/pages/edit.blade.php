<form action="{{ route('update', $movie->slug) }}" method="POST">
    @csrf
    <textarea id="description" name="description" rows="4" cols="50">
    </textarea>
    <br><br>
    <input type="submit" value="Submit">
</form>