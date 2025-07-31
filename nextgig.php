<?php
include 'gigs.php'; // this brings in the $gigs array

$today = date("Y-m-d");
$nextGig = null;

foreach ($gigs as $gig) {
  if ($gig["date"] >= $today) {
    $nextGig = $gig;
    break;
  }
}

if ($nextGig) {
  echo "🎸 Next Gig: " . date("F j, Y", strtotime($nextGig['date'])) .
       " — " . $nextGig['event'] .
       " at <a href='" . $nextGig['map'] . "' target='_blank'>" . $nextGig['venue'] . "</a>";
} else {
  echo "🚫 No upcoming gigs at the moment — stay tuned!";
}
?>

