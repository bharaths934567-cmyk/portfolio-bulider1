<?php
// Renders a portfolio with the selected template layout.
// Returns a full standalone HTML document (used by generate.php preview & export.php download).

function j($json) { $d = json_decode($json ?? '[]', true); return is_array($d) ? $d : []; }

function render_template(array $portfolio, array $template, bool $forExport = false): string {
  $template['template_file'] = $template['template_file'] ?? 'modern.php';
  $template['layout_type'] = $template['layout_type'] ?? 'modern';
  $template['accent_color'] = $template['accent_color'] ?? '#4f6df5';
  $templatePath = template_file_path($template['template_file']);
  ob_start();
  include $templatePath;
  return ob_get_clean();
}

function render_portfolio(array $p, array $t, bool $forExport = false): string {
    $edu  = j($p['education']);
    $skills = j($p['skills']);
    $proj = j($p['projects']);
    $exp  = j($p['experience']);
    $certifications = j($p['certifications'] ?? '[]');
    $social = j($p['social'] ?? '{}');
    $accent = e($t['accent_color']);
    $layout = $t['layout_type'];
    $name = e($p['full_name']);
    $isGrunge = ($t['template_file'] ?? '') === 'grunge.php';

    ob_start();
    ?>
<!DOCTYPE html><html><head><meta charset="utf-8">
<title><?= $name ?> — Portfolio</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',Arial,sans-serif; }
  body { background:#f4f6fb; color:#222; }
  .container { max-width:900px; margin:0 auto; background:#fff; min-height:100vh; padding:40px 50px; }
  a { color:<?= $accent ?>; }
<?php if ($isGrunge): ?>
  @import url('https://fonts.googleapis.com/css2?family=Anton&family=Barlow+Condensed:wght@400;600;700&display=swap');
  body { background:#121212 url('/portfolio-builder1/assets/images/grunge/bg.jpg') center/cover fixed; color:#f4f1ea; font-family:'Barlow Condensed',Arial,sans-serif; }
  .container { max-width:1180px; background:rgba(16,16,16,.88); min-height:100vh; padding:42px 7vw 80px; }
  .grunge-nav { display:flex; justify-content:flex-end; gap:30px; margin-bottom:70px; font-size:15px; font-weight:700; text-transform:uppercase; letter-spacing:1px; }
  .grunge-nav a { color:#f4f1ea; text-decoration:underline; text-underline-offset:4px; }
  .header { display:grid; grid-template-columns:1.25fr .75fr; gap:55px; align-items:end; border:0; padding:0; margin:0 0 60px; }
  .header:before { content:'AVAILABLE FOR WORK'; display:inline-block; position:absolute; margin-top:-290px; border:1px solid #f4f1ea; padding:14px 18px; font-size:13px; font-weight:700; letter-spacing:1px; }
  .header h1 { font-family:'Anton',Impact,sans-serif; font-size:clamp(4rem,12vw,10rem); line-height:.85; text-transform:uppercase; color:#f4f1ea; letter-spacing:1px; }
  .tagline { font-size:24px; line-height:1.05; max-width:380px; color:#f4f1ea; margin-bottom:22px; }
  .contact { text-align:left; margin:0; padding-top:0; color:#c8c3ba; font-size:16px; }
  .contact a { color:#f4f1ea; }
  .grunge-photo { width:100%; height:520px; object-fit:cover; filter:grayscale(1) contrast(1.1); border:1px solid #f4f1ea; }
  h2 { font-family:'Anton',Impact,sans-serif; color:#f4f1ea; border-bottom:1px solid #777; padding-bottom:8px; margin:44px 0 16px; font-size:30px; text-transform:uppercase; letter-spacing:1px; }
  .item { margin-bottom:18px; font-size:18px; }
  .item b { display:block; color:#fff; text-transform:uppercase; letter-spacing:.5px; }
  .muted { color:#aaa39a; font-size:16px; }
  .skills span { display:inline-block; border:1px solid #aaa39a; color:#f4f1ea; padding:5px 12px; margin:0 6px 8px 0; font-size:16px; }
  .grunge-about { max-width:600px; font-size:22px; line-height:1.1; }
<?php elseif ($layout === 'modern'): ?>
  .header { display:flex; gap:30px; border-left:8px solid <?= $accent ?>; padding-left:20px; margin-bottom:30px; }
  .header h1 { font-size:2.2rem; color:<?= $accent ?>; }
  .tagline { color:#666; font-size:1.1rem; }
  .contact { margin-left:auto; text-align:right; font-size:.9rem; color:#555; }
  h2 { color:<?= $accent ?>; border-bottom:2px solid <?= $accent ?>; padding-bottom:4px; margin:28px 0 12px; font-size:1.2rem; text-transform:uppercase; letter-spacing:1px; }
  .item { margin-bottom:14px; }
  .item b { display:block; }
  .muted { color:#777; font-size:.9rem; }
  .skills span { display:inline-block; background:<?= $accent ?>; color:#fff; padding:4px 12px; border-radius:20px; margin:0 6px 8px 0; font-size:.85rem; }
<?php elseif ($layout === 'classic'): ?>
  body { background:#fff; }
  .container { max-width:750px; text-align:center; }
  .header h1 { font-size:2.4rem; letter-spacing:2px; }
  .tagline { font-style:italic; color:#555; margin-top:6px; }
  .contact { margin-top:10px; font-size:.9rem; color:#666; }
  h2 { color:<?= $accent ?>; margin:30px 0 10px; font-size:1.15rem; border-bottom:1px solid #ddd; padding-bottom:6px; }
  .item { margin-bottom:14px; text-align:left; }
  .item b { color:<?= $accent ?>; }
  .muted { color:#777; font-size:.9rem; }
  .skills span { border:1px solid <?= $accent ?>; color:<?= $accent ?>; padding:3px 10px; border-radius:3px; margin:0 5px 8px 0; display:inline-block; font-size:.85rem; }
<?php else: /* creative */ ?>
  .header { background:<?= $accent ?>; color:#fff; padding:40px 30px; border-radius:14px; text-align:center; margin-bottom:30px; }
  .header h1 { font-size:2.4rem; }
  .tagline { opacity:.9; margin-top:6px; }
  .contact { margin-top:12px; font-size:.9rem; }
  .contact a { color:#fff; }
  h2 { color:<?= $accent ?>; margin:26px 0 12px; }
  .card { border:1px solid #eee; border-radius:10px; padding:14px 18px; margin-bottom:12px; box-shadow:0 2px 6px rgba(0,0,0,.05); }
  .item b { color:<?= $accent ?>; }
  .muted { color:#888; font-size:.9rem; }
  .skills span { background:#f0f0f0; border-left:4px solid <?= $accent ?>; padding:5px 12px; margin:0 6px 8px 0; display:inline-block; font-size:.85rem; }
<?php endif; ?>
</style></head><body><div class="container">
<?php if ($isGrunge): ?><nav class="grunge-nav"><a href="#home">1. Home</a><a href="#works">2. Works</a><a href="#about">3. About</a><a href="#contact">4. Contact</a></nav><?php endif; ?>

  <div class="header" id="home">
    <div><h1><?= $name ?></h1><div class="tagline"><?= e($p['tagline']) ?></div></div>
    <div class="contact">
      <?php if ($isGrunge): ?><img class="grunge-photo" src="/portfolio-builder1/assets/images/grunge/peter.jpg" alt="<?= $name ?> portrait"><?php endif; ?>
      <?= e($p['email']) ?><br><?= e($p['phone']) ?><br>
      <?php if ($p['website']): ?><a href="<?= e($p['website']) ?>"><?= e($p['website']) ?></a><?php endif; ?>
    </div>
  </div>

  <?php if (trim($p['about'] ?? '')): ?>
  <h2 id="about">About Me</h2><p class="<?= $isGrunge ? 'grunge-about' : '' ?>"><?= nl2br(e($p['about'])) ?></p>
  <?php endif; ?>

  <?php if ($skills): ?>
  <h2>Skills</h2><div class="skills">
    <?php foreach ($skills as $s): ?><span><?= e($s) ?></span><?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if ($edu): ?>
  <h2>Education</h2>
    <?php foreach ($edu as $r): ?>
    <div class="item"><b><?= e($r['degree'] ?? '') ?></b>
      <?= e($r['institution'] ?? '') ?> <span class="muted"><?= e($r['year'] ?? '') ?></span></div>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if ($proj): ?>
  <h2 id="works">Projects</h2>
    <?php foreach ($proj as $r): ?>
    <div class="<?= $layout === 'creative' ? 'card' : 'item' ?>">
      <b><?= e($r['title'] ?? '') ?></b>
      <p class="muted"><?= e($r['desc'] ?? '') ?></p>
      <?php if (!empty($r['link'])): ?><a href="<?= e($r['link']) ?>">View project →</a><?php endif; ?>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if ($exp): ?>
  <h2>Experience</h2>
    <?php foreach ($exp as $r): ?>
    <div class="item"><b><?= e($r['role'] ?? '') ?></b>
      <?= e($r['company'] ?? '') ?> <span class="muted"><?= e($r['duration'] ?? '') ?></span></div>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if ($certifications): ?><h2>Certifications</h2><?php foreach ($certifications as $r): ?><div class="item"><b><?= e($r['name'] ?? '') ?></b> <?= e($r['organization'] ?? '') ?><?php if (!empty($r['url'])): ?> · <a href="<?= e($r['url']) ?>">Credential</a><?php endif; ?></div><?php endforeach; ?><?php endif; ?>
  <?php if ($social): ?><h2 id="contact">Connect</h2><p><?php foreach ($social as $label => $url): ?><?php if ($url): ?><a href="<?= e($url) ?>"><?= e(ucfirst($label)) ?></a> &nbsp;<?php endif; ?><?php endforeach; ?></p><?php endif; ?>

</div></body></html>
<?php
    return ob_get_clean();
}
