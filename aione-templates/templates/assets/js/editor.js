jQuery(document).ready(function(){

  // Publish output from HTMl, CSS, and JS textareas in the iframe below
  onload=(document).onkeyup=function(){
    (document.getElementById("preview").contentWindow.document).write(
      html.value+"<style>"+css.value+"<\/style><script>"+js.value+"<\/script>"
    );
    (document.getElementById("preview").contentWindow.document).close()
  };

  // Pressing the Tab key inserts 2 spaces instead of shifting focus
  jQuery("textarea").keydown(function(event){
    if(event.keyCode === 9){
      var start = this.selectionStart;
      var end = this.selectionEnd;
      var $this = $(this);
      var value = $this.val();
      $this.val(value.substring(0, start)+"  "+value.substring(end));
      this.selectionStart = this.selectionEnd = start+1;
      event.preventDefault();
    }
  });

  // Store contents of textarea in sessionStorage
  jQuery("textarea").keydown(function(){
      sessionStorage[jQuery(this).attr("id")] = jQuery(this).val();
  });
  jQuery("#html").html(sessionStorage["html"]);
  jQuery("#css").html(sessionStorage["css"]);
  jQuery("#js").html(sessionStorage["js"]);
  function init() {
    if (sessionStorage["html"]) {
        jQuery("#html").val(sessionStorage["html"]);
      }
    if (sessionStorage["css"]) {
        jQuery("#css").val(sessionStorage["css"]);
      }  
    if (sessionStorage["js"]) {
        jQuery("#js").val(sessionStorage["js"]);
      }
  };



});
