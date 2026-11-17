<?php
if (isset($_POST['edit'])) {

    $id        = $_POST['id'];
    $nama      = $_POST['nama'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $jk        = $_POST['jk'];
    $alamat    = $_POST['alamat'];

    $query = mysqli_prepare($conn,
        "UPDATE anggota
         SET nama=?, tgl_lahir=?, jk=?, alamat=?
         WHERE id=?"
    );

    mysqli_stmt_bind_param(
        $query, "ssssi",
        $nama, $tgl_lahir, $jk, $alamat, $id
    );

    if (mysqli_stmt_execute($query)) {
        echo "Data berhasil diubah!";
    } else {
        echo "Data gagal diubah!";
    }
}
?>
