<?php
// ============================================================
// KONEKSI DATABASE
// ============================================================

$hostname = "127.0.0.1";
$username = "root";
$password = "";
$database = "restoran";

$koneksi = mysqli_connect($hostname, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");


// ============================================================
// PROSES CRUD
// ============================================================

$pesan = "";
$tipePesan = "";


// ============================================================
// CREATE - TAMBAH DATA
// ============================================================

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["aksi"])) {

    $aksi = $_POST["aksi"];


    // --------------------------------------------------------
    // CREATE
    // --------------------------------------------------------

    if ($aksi === "tambah") {

        $nama = trim($_POST["nama"] ?? "");
        $tgl_lahir = $_POST["tgl_lahir"] ?? "";
        $jk = $_POST["jk"] ?? "";
        $alamat = trim($_POST["alamat"] ?? "");

        // Validasi
        if ($nama === "" || $tgl_lahir === "" || $jk === "" || $alamat === "") {

            $pesan = "Semua data harus diisi.";
            $tipePesan = "danger";

        } else {

            // Cek apakah data yang sama sudah ada
            $cek = mysqli_prepare(
                $koneksi,
                "SELECT id FROM anggota
                 WHERE nama = ?
                 AND tgl_lahir = ?
                 AND jk = ?
                 AND alamat = ?"
            );

            mysqli_stmt_bind_param(
                $cek,
                "ssss",
                $nama,
                $tgl_lahir,
                $jk,
                $alamat
            );

            mysqli_stmt_execute($cek);

            $hasilCek = mysqli_stmt_get_result($cek);

            if (mysqli_num_rows($hasilCek) > 0) {

                $pesan = "Data tersebut sudah ada, tidak boleh duplikat.";
                $tipePesan = "warning";

            } else {

                // Insert data
                $input = mysqli_prepare(
                    $koneksi,
                    "INSERT INTO anggota
                    (nama, tgl_lahir, jk, alamat, tgl_input)
                    VALUES (?, ?, ?, ?, ?)"
                );

                date_default_timezone_set("Asia/Jakarta");
                $tgl_input = date("Y-m-d H:i:s");

                mysqli_stmt_bind_param(
                    $input,
                    "sssss",
                    $nama,
                    $tgl_lahir,
                    $jk,
                    $alamat,
                    $tgl_input
                );

                if (mysqli_stmt_execute($input)) {

                    $pesan = "Data anggota berhasil ditambahkan.";
                    $tipePesan = "success";

                } else {

                    $pesan = "Data gagal ditambahkan.";
                    $tipePesan = "danger";
                }

                mysqli_stmt_close($input);
            }

            mysqli_stmt_close($cek);
        }
    }


    // ========================================================
    // UPDATE - UBAH DATA
    // ========================================================

    elseif ($aksi === "update") {

        $id = intval($_POST["id"] ?? 0);
        $nama = trim($_POST["nama"] ?? "");
        $tgl_lahir = $_POST["tgl_lahir"] ?? "";
        $jk = $_POST["jk"] ?? "";
        $alamat = trim($_POST["alamat"] ?? "");

        if (
            $id <= 0 ||
            $nama === "" ||
            $tgl_lahir === "" ||
            $jk === "" ||
            $alamat === ""
        ) {

            $pesan = "Data edit belum lengkap.";
            $tipePesan = "danger";

        } else {

            // Cek duplikat, tetapi abaikan ID yang sedang diedit
            $cek = mysqli_prepare(
                $koneksi,
                "SELECT id FROM anggota
                 WHERE nama = ?
                 AND tgl_lahir = ?
                 AND jk = ?
                 AND alamat = ?
                 AND id != ?"
            );

            mysqli_stmt_bind_param(
                $cek,
                "ssssi",
                $nama,
                $tgl_lahir,
                $jk,
                $alamat,
                $id
            );

            mysqli_stmt_execute($cek);

            $hasilCek = mysqli_stmt_get_result($cek);

            if (mysqli_num_rows($hasilCek) > 0) {

                $pesan = "Data tersebut sudah ada.";
                $tipePesan = "warning";

            } else {

                $update = mysqli_prepare(
                    $koneksi,
                    "UPDATE anggota
                     SET nama = ?,
                         tgl_lahir = ?,
                         jk = ?,
                         alamat = ?
                     WHERE id = ?"
                );

                mysqli_stmt_bind_param(
                    $update,
                    "ssssi",
                    $nama,
                    $tgl_lahir,
                    $jk,
                    $alamat,
                    $id
                );

                if (mysqli_stmt_execute($update)) {

                    $pesan = "Data anggota berhasil diperbarui.";
                    $tipePesan = "success";

                } else {

                    $pesan = "Data gagal diperbarui.";
                    $tipePesan = "danger";
                }

                mysqli_stmt_close($update);
            }

            mysqli_stmt_close($cek);
        }
    }


    // ========================================================
    // DELETE - HAPUS DATA
    // ========================================================

    elseif ($aksi === "hapus") {

        $id = intval($_POST["id"] ?? 0);

        if ($id <= 0) {

            $pesan = "ID data tidak valid.";
            $tipePesan = "danger";

        } else {

            $hapus = mysqli_prepare(
                $koneksi,
                "DELETE FROM anggota WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $hapus,
                "i",
                $id
            );

            if (mysqli_stmt_execute($hapus)) {

                if (mysqli_stmt_affected_rows($hapus) > 0) {

                    $pesan = "Data anggota berhasil dihapus.";
                    $tipePesan = "success";

                } else {

                    $pesan = "Data tidak ditemukan.";
                    $tipePesan = "warning";
                }

            } else {

                $pesan = "Data gagal dihapus.";
                $tipePesan = "danger";
            }

            mysqli_stmt_close($hapus);
        }
    }
}


// ============================================================
// AMBIL DATA UNTUK READ
// ============================================================

$query = "SELECT * FROM anggota ORDER BY id DESC";
$baca = mysqli_query($koneksi, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ferdy - XII TKJ 5</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    >

    <link
        rel="shortcut icon"
        href="https://upload-wikimedia-org.translate.goog/wikipedia/en/a/ae/Los_Pollos_Hermanos_logo.png?utm_source=en.wikipedia.org&utm_campaign=index&utm_content=original&_x_tr_sl=en&_x_tr_tl=id&_x_tr_hl=id&_x_tr_pto=imgs"
        type="image/x-icon"
    >

</head>


<body>

<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar navbar-expand-lg bg-primary navbar-dark">

    <div class="container-fluid">

        <a class="navbar-brand" href="simpel1.php">

            <img
                src="https://upload-wikimedia-org.translate.goog/wikipedia/en/a/ae/Los_Pollos_Hermanos_logo.png?utm_source=en.wikipedia.org&utm_campaign=index&utm_content=original&_x_tr_sl=en&_x_tr_tl=id&_x_tr_hl=id&_x_tr_pto=imgs"
                alt="Los Pollos"
                width="50px"
            >

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="navbarSupportedContent"
        >

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">

                    <a
                        class="nav-link active"
                        href="https://ferdyportofolio.web.id"
                    >
                        CEO
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="https://id.wikipedia.org/wiki/Breaking_Bad"
                    >
                        Film
                    </a>

                </li>


                <li class="nav-item dropdown">

                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                    >
                        Pilih
                    </a>


                    <ul class="dropdown-menu">

                        <li>
                            <a
                                class="dropdown-item"
                                href="https://ferdyportofolio.web.id"
                            >
                                CEO
                            </a>
                        </li>


                        <li>
                            <a
                                class="dropdown-item"
                                href="https://id.wikipedia.org/wiki/Breaking_Bad"
                            >
                                Film
                            </a>
                        </li>

                    </ul>

                </li>

            </ul>


            <form class="d-flex">

                <input
                    class="form-control me-2"
                    type="search"
                    placeholder="Search"
                >

                <button
                    class="btn btn-outline-danger"
                    type="submit"
                >
                    Search
                </button>

            </form>

        </div>

    </div>

</nav>


<!-- =========================================================
     CONTENT
========================================================= -->

<div class="my-5 text-center">

    <div class="container">

        <h1>Project Los Pollos Hermanos</h1>

        <h2>
            Sistem Pendataan Karyawan Restoran Digital Sederhana
        </h2>


        <img
            src="https://upload-wikimedia-org.translate.goog/wikipedia/en/a/ae/Los_Pollos_Hermanos_logo.png?utm_source=en.wikipedia.org&utm_campaign=index&utm_content=original&_x_tr_sl=en&_x_tr_tl=id&_x_tr_hl=id&_x_tr_pto=imgs"
            alt="gambar eror"
        >

        <hr>


        <!-- =================================================
             STATUS KONEKSI
        ================================================== -->

        <div class="position-relative">

            <div class="position-absolute bottom-0 end-0">

                <p>

                    Koneksi

                    <span
                        class="translate-middle badge border border-light rounded-circle bg-success p-2"
                    >

                        <span class="visually-hidden">
                            Koneksi berhasil
                        </span>

                    </span>

                </p>

            </div>

        </div>


        <h3>Data Anggota</h3>


        <!-- =================================================
             ALERT
        ================================================== -->

        <?php if ($pesan !== ""): ?>

            <div
                class="alert alert-<?= $tipePesan ?> alert-dismissible fade show"
                role="alert"
            >

                <?= htmlspecialchars($pesan) ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <!-- =================================================
             TOMBOL TAMBAH
        ================================================== -->

        <div class="text-end my-1">

            <button
                type="button"
                class="btn btn-sm btn-success"
                data-bs-toggle="modal"
                data-bs-target="#Modal-1"
            >
                Tambah Anggota
            </button>

        </div>


        <!-- =================================================
             MODAL TAMBAH DATA
        ================================================== -->

        <div
            class="modal fade"
            id="Modal-1"
            tabindex="-1"
        >

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">

                        <h1 class="modal-title fs-5">
                            Tambah Data Anggota
                        </h1>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>

                    </div>


                    <form method="POST">

                        <input
                            type="hidden"
                            name="aksi"
                            value="tambah"
                        >


                        <div class="modal-body">

                            <div class="mb-3">

                                <label class="form-label">
                                    Nama
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="nama"
                                    placeholder="Nama Lengkap"
                                    required
                                >

                            </div>


                            <div class="mb-3">

                                <label class="form-label">
                                    Tanggal Lahir
                                </label>

                                <input
                                    type="date"
                                    class="form-control"
                                    name="tgl_lahir"
                                    required
                                >

                            </div>


                            <div class="mb-3">

                                <label class="form-label">
                                    Jenis Kelamin
                                </label>

                                <select
                                    class="form-select"
                                    name="jk"
                                    required
                                >

                                    <option value="">
                                        Pilih Salah Satu
                                    </option>

                                    <option value="Laki-laki">
                                        Laki-laki
                                    </option>

                                    <option value="Perempuan">
                                        Perempuan
                                    </option>

                                </select>

                            </div>


                            <div class="mb-3">

                                <label class="form-label">
                                    Alamat
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="alamat"
                                    placeholder="Alamat Lengkap"
                                    required
                                >

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                Simpan
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- =================================================
             TABEL DATA
        ================================================== -->

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <thead class="table-dark">

                    <tr>

                        <th>No</th>

                        <th>Nama</th>

                        <th>Jenis Kelamin</th>

                        <th>Tanggal Lahir</th>

                        <th>Alamat</th>

                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $no = 1;

                if (mysqli_num_rows($baca) > 0):

                    while ($data = mysqli_fetch_assoc($baca)):

                ?>

                    <tr>

                        <td>
                            <?= $no++ ?>
                        </td>


                        <td>
                            <?= htmlspecialchars($data["nama"]) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars($data["jk"]) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars($data["tgl_lahir"]) ?>
                        </td>


                        <td>
                            <?= htmlspecialchars($data["alamat"]) ?>
                        </td>


                        <td>

                            <!-- EDIT -->

                            <button
                                type="button"
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#edit<?= $data["id"] ?>"
                            >
                                Edit
                            </button>


                            <!-- DELETE -->

                            <button
                                type="button"
                                class="btn btn-sm btn-danger"
                                d
