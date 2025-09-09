<?php
require_once __DIR__.'/../functions.php';
if (session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__.'/auth.php';

$pdo = getPDO();
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = null; $success = null;

// Ensure table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

try{
  if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!csrf_verify($_POST['csrf'] ?? '')) throw new Exception('CSRF token tidak valid.');
    if($action==='create'){
      $u = trim($_POST['username'] ?? '');
      $p = trim($_POST['password'] ?? '');
      if($u==='' || $p==='') throw new Exception('Username & Password wajib diisi.');
      $hash = password_hash($p, PASSWORD_DEFAULT);
      $st = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?,?)");
      $st->execute([$u, $hash]);
      header('Location: users.php?msg=created'); exit;
    } elseif($action==='update' && $id){
      $u = trim($_POST['username'] ?? '');
      $p = trim($_POST['password'] ?? '');
      if($u==='') throw new Exception('Username wajib diisi.');
      if($p!==''){
        $hash = password_hash($p, PASSWORD_DEFAULT);
        $st = $pdo->prepare("UPDATE admins SET username=?, password_hash=? WHERE id=?");
        $st->execute([$u, $hash, $id]);
      }else{
        $st = $pdo->prepare("UPDATE admins SET username=? WHERE id=?");
        $st->execute([$u, $id]);
      }
      header('Location: users.php?msg=updated'); exit;
    }
  } elseif($action==='delete' && $id){
    // prevent deleting oneself or last admin
    $st = $pdo->query("SELECT COUNT(*) c FROM admins");
    $count = (int)$st->fetch()['c'];
    if($count <= 1) throw new Exception('Tidak bisa menghapus admin terakhir.');
    $st = $pdo->prepare("SELECT username FROM admins WHERE id=?");
    $st->execute([$id]);
    $row = $st->fetch();
    if(!$row) throw new Exception('User tidak ditemukan.');
    if(!empty($_SESSION['admin_username']) && $_SESSION['admin_username']===$row['username']){
      throw new Exception('Tidak bisa menghapus akun yang sedang dipakai.');
    }
    $pdo->prepare("DELETE FROM admins WHERE id=?")->execute([$id]);
    header('Location: users.php?msg=deleted'); exit;
  }
}catch(Throwable $e){
  $error = $e->getMessage();
}

include '../header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Admin Users</h3>
  <a href="users.php?action=new" class="btn btn-primary"><i class="fa fa-user-plus"></i> Tambah Admin</a>
</div>
<?php if(isset($_GET['msg'])): ?><div class="alert alert-success">Sukses <?php echo esc($_GET['msg']); ?>.</div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?php echo esc($error); ?></div><?php endif; ?>

<?php if($action==='new' || ($action==='edit' && $id)):
  $row = ['username'=>''];
  if($action==='edit'){
    $st = $pdo->prepare("SELECT * FROM admins WHERE id=?");
    $st->execute([$id]);
    $row = $st->fetch();
  }
?>
<div class="card p-3">
  <form method="post" action="users.php?action=<?php echo $action==='new'?'create':'update&id='.(int)$id; ?>">
    <input type="hidden" name="csrf" value="<?php echo esc(csrf_token()); ?>">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Username</label>
        <input class="form-control" name="username" required value="<?php echo esc($row['username']); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label"><?php echo $action==='new'?'Password':'Password Baru (opsional)'; ?></label>
        <input class="form-control" name="password" type="password" <?php echo $action==='new'?'required':''; ?>>
      </div>
      <div class="col-12">
        <button class="btn btn-primary"><?php echo $action==='new'?'Simpan':'Update'; ?></button>
        <a href="users.php" class="btn btn-outline-secondary">Batal</a>
      </div>
    </div>
  </form>
</div>
<?php else:
  $rows = $pdo->query("SELECT id, username, created_at FROM admins ORDER BY id DESC")->fetchAll(); ?>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead><tr><th>ID</th><th>Username</th><th>Dibuat</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?php echo (int)$r['id']; ?></td>
          <td><?php echo esc($r['username']); ?></td>
          <td><?php echo esc($r['created_at']); ?></td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="users.php?action=edit&id=<?php echo (int)$r['id']; ?>"><i class="fa fa-pen"></i> Edit</a>
            <a class="btn btn-sm btn-outline-danger" href="users.php?action=delete&id=<?php echo (int)$r['id']; ?>" onclick="return confirm('Hapus admin ini?')"><i class="fa fa-trash"></i> Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include '../footer.php'; ?>
