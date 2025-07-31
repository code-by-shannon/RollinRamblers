<?php include 'head.php'; ?>
<?php include 'header.php'; ?>

<link rel="stylesheet" href="store.css">

<!-- STORE -->
<div id="merch" class="store">
  <h2>Ramblin' Store</h2>

  <!-- CD -->
  <div class="item">
    <img src="imgs/RRCD.jpeg" alt="Rollin' Ramblers CD">
    <p>CD – $5</p>
    <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="hosted_button_id" value="GXHDCAFX5VKLA">
      <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_cart_LG.gif" border="0" name="submit" alt="Add CD to Cart">
      <img alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
  </div>

  <!-- T-Shirt -->
  <div class="item">
    <img src="imgs/RR_T.png" alt="Rollin' Ramblers T-shirt">
    <p>T-Shirt – $25 (All Sizes)</p>
    <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="hosted_button_id" value="VPH5WGU4TQUK2">
      <label for="tshirt-size">Select Size:</label>
      <select name="os0" id="tshirt-size">
        <option value="Small">Small</option>
        <option value="Medium">Medium</option>
        <option value="Large">Large</option>
        <option value="X-Large">X-Large</option>
      </select>
      <br>
      <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_cart_LG.gif" border="0" name="submit" alt="Add T-Shirt to Cart">
      <img alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
  </div>

  <!-- View Cart -->
  <div class="cart">
    <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----(your encrypted PayPal cart data)-----END PKCS7-----">
      <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_viewcart_LG.gif" border="0" name="submit" alt="View Cart">
      <img alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>
