<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<link rel="stylesheet" href="contact.css">
<body>
<!-- HEADER -->
<?php include 'header.php'; ?>
    
<!-- CONTACT FORM -->
<h1>Contact Form</h1>
  <hr>
  <h2>For booking or other related queries</h2>
  <p>Send us a message!</p>
  <form id="contact-form" action="mail.php" method="POST">
      <input type = "text" name = "name" id="name" placeholder = "Name" required> 
      <input type = "text" name = "email" id="email" placeholder = "joe@email.com" required>
      <textarea id="message" name = "message" rows="6" required placeholder="Please put booking inquiries or other questions here . . . "></textarea>
      <button type="submit">Book Us!</button>      
  </form>
  
  <figure id='contact-photo'>
    <img id="bookingPhoto" src='imgs/contact.png' alt='a picture of the rollin ramblers playing live on stage at cowboy country in long beach california during the holidays'/>
    <figcaption>Ramblin at the Cowboy Country Long Beach, Christmas 2023</figcaption>
  </figure>
<hr>

<!-- STAGE PLOT AND RATES -->
<h2>Stage Plot</h2>
<div class="stage-plot">
  <!-- Row 1: backline -->
  <div class="plot-row">
    <div class="position">Pedal Steel<br>Stage Right<br>Amp<br>Vocal Mic</div>
    <div class="position">Drums<br>Center Back<br>Kick / Snare / OH </div>
    <div class="position">Bass<br>Stage Left<br>Bass Amp<br>Vocal Mic</div>
  </div>

  <!-- Row 2: front line -->
  <div class="plot-row">
    <div class="position">Lead Vocals / Rhythm Guitar<br>Front Center Right<br>Vocal Mic + Guitar Amp + DI</div>
    <div class="position">Lead Vocals / Lead Guitar<br>Front Center Left<br>Vocal Mic + Guitar Amp</div>
  </div>
</div>


  <h2>Booking Rates</h2>
  <div class="rates">
    <ul>
      <li><strong>Standard Show (3x45min sets, local):</strong> $600</li>
      <li><strong>Out-of-Town (60+ miles):</strong> $800 + lodging</li>
      <li><strong>Acoustic Duo (1 hour set):</strong> $200</li>
      <li><strong>Weddings & Private Events:</strong> Contact for custom quote</li>
    </ul>
    <p>All shows include basic PA. We bring the good times—you bring the outlets and a few cold ones.</p>
  </div>
   

    

   <!-- FOOTER - SOCIAL MEDIA ICONS-->
   <?php include 'footer.php'; ?>


    <script>
  window.addEventListener('DOMContentLoaded', () => {
    const honeypot = document.createElement('input');
    honeypot.type = 'text';
    honeypot.name = 'additional_notes_94xY';
    honeypot.style.position = 'absolute';
    honeypot.style.left = '-9999px';
    honeypot.tabIndex = '-1';

    const form = document.getElementById('contact-form');
    if (form) {
      form.appendChild(honeypot);
    }
  });
</script>

    
</body>
</html>