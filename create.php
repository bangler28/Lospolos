<?php
if (isset($_POST['simpan'])) {

    $nama      = $_POST['nama'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $jk        = $_POST['jk'];
    $alamat    = $_POST['alamat'];

    // Cek duplikat
    $cek = mysqli_prepare($conn,
        "SELECT id FROM anggota WHERE nama = ?"
    );

    mysqli_stmt_bind_param($cek, "s", $nama);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {
        echo "Nama sudah terdaftar!";
    } else {

        $query = mysqli_prepare($conn,
            "INSERT INTO anggota (nama, tgl_lahir, jk, alamat)
             VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $query, "ssss",
            $nama, $tgl_lahir, $jk, $alamat
        );

        if (mysqli_stmt_execute($query)) {
            echo "Data berhasil ditambahkan!";
        } else {
            echo "Data gagal ditambahkan!";
        }
    }
}
?>
