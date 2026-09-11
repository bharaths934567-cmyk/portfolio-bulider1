<?php
require_once 'config.php';
require_login();
$tid = $_SESSION['template_id'] ?? 0;
if (!$tid) { header('Location: choose_template.php'); exit; }
$templateStmt = $pdo->prepare('SELECT * FROM templates WHERE id = ? AND status = "active"');
$templateStmt->execute([(int)$tid]);
$selectedTemplate = $templateStmt->fetch(PDO::FETCH_ASSOC);
if (!$selectedTemplate) { exit('Template unavailable.'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
    $clean = function($arr) {
        $out = [];
        foreach ((array)$arr as $row) {
            $row = array_map('trim', (array)$row);
            if (implode('', $row) !== '') $out[] = $row;
        }
        return $out;
    };
    $customData = array_map('trim', $_POST['custom'] ?? []);
    foreach ((array)($_FILES['custom_file']['name'] ?? []) as $key => $_) {
      if (!empty($_FILES['custom_file']['tmp_name'][$key])) {
        $customData[$key] = image_data_url(['error' => $_FILES['custom_file']['error'][$key], 'size' => $_FILES['custom_file']['size'][$key], 'tmp_name' => $_FILES['custom_file']['tmp_name'][$key]]);
      }
    }
    $fullName = trim($_POST['full_name'] ?? ($customData['name'] ?? ''));
    $tagline = trim($_POST['tagline'] ?? ($customData['job_title'] ?? ''));
    $about = trim($_POST['about'] ?? ($customData['about'] ?? ''));
    $email = trim($_POST['email'] ?? ($customData['email'] ?? ''));
    if (mb_strlen($fullName) < 2) { exit('Please enter your name.'); }
    $education = $clean($_POST['education'] ?? []);
    $skills    = array_values(array_filter(array_map('trim', (array)($_POST['skills'] ?? []))));
    $projects  = $clean($_POST['projects'] ?? []);
    $experience= $clean($_POST['experience'] ?? []);
    $certifications = $clean($_POST['certifications'] ?? []);
    $social = array_map('trim', $_POST['social'] ?? []);

    $stmt = $pdo->prepare("INSERT INTO portfolios
        (user_id, template_id, full_name, tagline, about, email, phone, website,
         education, skills, projects, experience, certifications, social, custom_data)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $_SESSION['user_id'], $tid,
        $fullName, $tagline, $about,
        $email, trim($_POST['phone'] ?? ($customData['phone'] ?? '')), trim($_POST['website'] ?? ($customData['website'] ?? '')),
        json_encode($education), json_encode($skills),
        json_encode($projects), json_encode($experience), json_encode($certifications), json_encode($social), json_encode($customData)
    ]);
    header('Location: generate.php?id=' . $pdo->lastInsertId());
    exit;
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Build Portfolio</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<nav class="topnav"><span class="brand">🚀 Portfolio Builder</span>
<span><a href="choose_template.php">← Back to Templates</a> <a href="my_portfolios.php">My Portfolios</a></span></nav>
<div class="wrap">
<h1>Step 2 · Enter Your Details</h1>
<form method="post" id="buildForm" enctype="multipart/form-data">
<?= csrf_field() ?>

<?php if (!empty($selectedTemplate['template_html'])): ?>
  <fieldset><legend><?= e($selectedTemplate['name']) ?> details</legend>
  <?php foreach (json_decode($selectedTemplate['fields_json'] ?? '[]', true) ?: [] as $field): ?><label><?= e($field['label'] ?? $field['key']) ?><?php if (($field['type'] ?? 'text') === 'file'): ?><input type="file" name="custom_file[<?= e($field['key']) ?>]" accept="image/jpeg,image/png,image/webp"><?php else: ?><textarea name="custom[<?= e($field['key']) ?>]" placeholder="<?= e($field['label'] ?? $field['key']) ?>"></textarea><?php endif; ?></label><?php endforeach; ?>
  </fieldset>
<?php else: ?>

  <fieldset><legend>Basic Info</legend>
    <input name="full_name" placeholder="Full name *" required>
    <input name="tagline" placeholder="Tagline / headline (e.g. Full-Stack Developer)">
    <input name="email" type="email" placeholder="Email">
    <input name="phone" placeholder="Phone">
    <input name="website" placeholder="Website / GitHub URL">
    <textarea name="about" placeholder="Short summary about yourself"></textarea>
  </fieldset>

  <fieldset><legend>Education</legend>
    <div id="eduRows"></div>
    <button type="button" class="btn ghost" onclick="addEdu()">+ Add Education</button>
  </fieldset>

  <fieldset><legend>Skills</legend>
    <div id="skillRows"></div>
    <button type="button" class="btn ghost" onclick="addSkill()">+ Add Skill</button>
  </fieldset>

  <fieldset><legend>Projects</legend>
    <div id="projRows"></div>
    <button type="button" class="btn ghost" onclick="addProj()">+ Add Project</button>
  </fieldset>

  <fieldset><legend>Experience</legend>
    <div id="expRows"></div>
    <button type="button" class="btn ghost" onclick="addExp()">+ Add Experience</button>
  </fieldset>

  <fieldset><legend>Certifications</legend>
    <div id="certRows"></div>
    <button type="button" class="btn ghost" onclick="addCert()">+ Add Certification</button>
  </fieldset>

  <fieldset><legend>Social Links</legend>
    <input name="social[github]" placeholder="GitHub URL">
    <input name="social[linkedin]" placeholder="LinkedIn URL">
    <input name="social[twitter]" placeholder="Twitter / X URL">
    <input name="social[instagram]" placeholder="Instagram URL">
  </fieldset>
<?php endif; ?>

  <br><button class="btn big">Generate Portfolio ➜</button>
</form>
</div>

<script>
function addEdu(){
  document.getElementById('eduRows').insertAdjacentHTML('beforeend',
   `<div class="row">
      <input name="education[][degree]" placeholder="Degree (e.g. B.Tech CSE)">
      <input name="education[][institution]" placeholder="Institution">
      <input name="education[][year]" placeholder="Year (e.g. 2020 - 2024)">
      <button type="button" class="del" onclick="this.parentElement.remove()">✕</button>
    </div>`);
}
function addSkill(){
  document.getElementById('skillRows').insertAdjacentHTML('beforeend',
   `<div class="row"><input name="skills[]" placeholder="Skill (e.g. PHP, MySQL)">
    <button type="button" class="del" onclick="this.parentElement.remove()">✕</button></div>`);
}
function addProj(){
  document.getElementById('projRows').insertAdjacentHTML('beforeend',
   `<div class="row">
      <input name="projects[][title]" placeholder="Project title">
      <input name="projects[][desc]" placeholder="Short description">
      <input name="projects[][link]" placeholder="Link (optional)">
      <button type="button" class="del" onclick="this.parentElement.remove()">✕</button>
    </div>`);
}
function addExp(){
  document.getElementById('expRows').insertAdjacentHTML('beforeend',
   `<div class="row">
      <input name="experience[][role]" placeholder="Job title / role">
      <input name="experience[][company]" placeholder="Company">
      <input name="experience[][duration]" placeholder="Duration (e.g. 2023 - Present)">
      <button type="button" class="del" onclick="this.parentElement.remove()">✕</button>
    </div>`);
}
function addCert(){
  document.getElementById('certRows').insertAdjacentHTML('beforeend',
   `<div class="row"><input name="certifications[][name]" placeholder="Certificate name"><input name="certifications[][organization]" placeholder="Issuing organization"><input name="certifications[][url]" placeholder="Credential URL"><button type="button" class="del" onclick="this.parentElement.remove()">✕</button></div>`);
}
addEdu(); addSkill(); addProj(); addExp(); addCert();
</script>
</body></html>
