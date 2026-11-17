<?php
if (isset($_POST['hapus'])) {

    $id = $_POST['id'];

    $query = mysqli_prepare($conn,
        "DELETE FROM anggota WHERE id=?"
    );

    mysqli_stmt_bind_param($query, "i", $id);

    if (mysqli_stmt_execute($query)) {
        echo "Data berhasil dihapus!";
    } else {
        echo "Data gagal dihapus!";
    }
}
?>
