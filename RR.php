<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<link rel="stylesheet" href="RR.css">
<body>
<!-- HERO VIDEO HIGHLIGHT REEL -->
  <div class="top-video-container">
    <iframe
      src="https://www.youtube.com/embed/4T_sDHq4ai4?autoplay=1&mute=1&controls=1&rel=0&playsinline=1"
      title="YouTube video player"
      frameborder="0"
      allow="autoplay; encrypted-media"
      allowfullscreen>
    </iframe>
  </div>

<!-- HEADER -->
<?php include 'header.php'; ?>

<!-- H1 and NEW and IMPROVED GIG NAGGER -->  
<h1>Rollin' Ramblers</h1>
<p id='gignag'><?php include 'nextgig.php'; ?></p>

<!-- HEADER HERO IMAGE HERO TEXT-->    
  <div class="header"> 
    <img id="hero-image" src="imgs/RRlogo.jpg">
    <div class="biography">
        <h2 id="bio-title">Origin Story</h3>
        <p>It all began in the spring of 2018, in a dusty garage somewhere in the belly of Pico Rivera.  Ramblin' Czar, no longer able or willing to deny his yearning to sing, write and perform Honky Tonk and Outlaw songs, asked his long time amigo and former rock and roll band mate Jose, if he had the hankerin' to start a new outlaw band.  "Hell yeah!" he exclaimed, or similiar words to that effect.  With bass, rhythm guitar and vocals in place (along with a bulging satchel full of all original, outlaw tunes in the vein of Merle Haggard, Waylon Jennings, Dwight Yoakam and Hank III,) they went about assembling the rest of the band.  It fell into place quickly enough adding Shannon on lead guitar and vocals and soon thereafter Charlie, a highly sought after pedal steel player.  Thus, a true, outlaw, honky tonk sound was cemented.  After 2 years of playing countess gigs all over Orange, LA and San Bernadino counties, they went on touring hiatus to record their first ful length album "Hellraisin' and Heartbroken," which was released in July of 2020.  It's an album full of songs about hittin' the road and being free, fast cars and crazy women and sometimes, happier songs about a long and well deserved dirt nap in a pine box under the desert moon. The Rollin' Ramblers set is replete with hard rockers and slow sad ones alike.  The Ramblers take pride not only in their musicianship also in forging a new trail while honoring the traditional country sounds of the past.</p>
    </div>
  </div>  

<hr>

<!-- PHOTO GALLERY AND MEMBER BIOS-->

<h2 id="photo-gallery-heading">Ramblin' Pics</h2>
    
  <div class="gallery">
    <!-- SUPPLY AND DEMAND PIC -->
    <div class="card">
      <img class='gallery-image' src="imgs/Supply.jpg">
    </div>
    
    <!-- CESAR BIO PIC-->
    <div class="card">
      <img class='gallery-image' 
          src="imgs/Cesar.jpg" 
          data-front="Cesar.jpg"
          data-back="cesarBioBack3.png"
          alt="Cesar Michel with his famous beard and sunglasses posing in a jean vest holding a beer">
          <div class="flip-hint">Tap to flip</div>
    </div>
    <!-- JOSE BIO PIC-->
    <div class="card">
      <img class='gallery-image' 
          src="imgs/Jose.jpg" 
          data-front="Jose.jpg"
          data-back="joseBioBack2.png"
          alt="Jose playing bass with a western shirt, cowboy hat and sunglasses">
          <div class="flip-hint">Tap to flip</div>
    </div>
    <!-- ROLLIN RAMBLERS AT CINEMA BAR-->
    <div class="card">
      <img class='gallery-image' id="ari" src="imgs/RRcinema.jpg">
    </div>
    <!-- ARI'S OLD DRUM SET-->
    <div class="card">
      <img class='gallery-image' src="imgs/Adrums.jpg">
    </div>
    <!-- CHARLIE BIO PIC-->
    <div class="card">
      <img  class='gallery-image'
          src="imgs/charlie.jpg" 
          data-front="charlie.jpg"
          data-back="charlieBioBack2.png"
          alt="Charlie looking happy and wearing western attire playing the pedal steel">
          <div class="flip-hint">Tap to flip</div>
    </div>
    <!-- SHANNON BIO PIC-->
    <div class="card">
      <img  class='gallery-image' 
          src="imgs/sguitar.jpg"
          data-front="sguitar.jpg"
          data-back="shannonBioBack2.png"
          alt="Shannon's hands on his Brad Paisely silver sparkle tele playing at the top of the guitar neck wearing a western shirt">
          <div class="flip-hint">Tap to flip</div>
    </div>
    <!-- BACK OF ALBUM-->
    <div class="card">
      <img class='gallery-image' src="imgs/albumback.jpg">
    </div>
    <!-- FRANK BIO-->
    <div class="card">
      <img class='gallery-image'
          src="imgs/frank.png"
          data-front="frank.png"
          data-back="frankBioBack2.png"
          alt='Frank in his natural habitat, behind the drums'>
          <div class="flip-hint">Tap to flip</div>
    </div>
  </div>

  <!-- FOOTER - SOCIAL MEDIA ICONS-->
  <?php include 'footer.php'; ?>
      

<script src="RR.js"></script>
</body>
</html>