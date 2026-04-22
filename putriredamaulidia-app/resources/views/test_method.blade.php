<form action="/simpan" method="POST">
    @csrf
    <button type="submit">Simpan Data (POST)</button>
</form>
<form action="/update/1" method="POST">
    @csrf
    @method('PUT')
    <button type="submit">Update Data (PUT)</button>
</form>
<form action="/update2/1" method="POST">
    @csrf
    @method('PATCH')
    <button type="submit">Update Data (PATCH)</button>
</form>
<form action="/hapus/1" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit">Hapus Data (DELETE)</button>
</form>