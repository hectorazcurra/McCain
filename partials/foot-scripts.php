<script>
// Highlight active sidebar link
document.querySelectorAll('.sidebar a').forEach(function(a) {
  if (a.href === window.location.href.split('?')[0]) {
    a.classList.add('active');
  }
});
</script>
