<?php
$query = mysqli_query($conn, "SELECT * FROM anggota ORDER BY id DESC");

while ($data = mysqli_fetch_assoc($query)) {
?>

<tr>
    <td><?= $data['id']; ?></td>
    <td><?= htmlspecialchars($data['nama']); ?></td>
    <td><?= htmlspecialchars($data['jk']); ?></td>
    <td><?= htmlspecialchars($data['tgl_lahir']); ?></td>
    <td><?= htmlspecialchars($data['alamat']); ?></td>
</tr>

<?php
}
?>
