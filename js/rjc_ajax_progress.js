jQuery("#proceso").on("click",function(e){
   e.preventDefault();
   var elem = document.getElementById("rjc_barra"); 
   if (elem) {
      clearTimeout(progreso);
     setTimeout(progreso, 1000);
   } 
   jQuery.ajax({
      url : rjc_vars.ajaxurl,
      type: "post",
      data: {action : "rjc_proceso"},
      beforeSend: function(){jQuery('#proceso').html("Procesando ...");},
   });
});

function progreso() {
   var datos=[0,0,0];
   jQuery.ajax({
      url: rjc_vars.ajaxurl,
      type: "post",
      data: {action : "rjc_progreso"}, 
      success: function(data) {
         datos=data.split("#rol#");
         if (datos[1]>0) {
            var texto=datos[1]+" elementos ("+datos[2]+" procesados)";
            if (datos[4]<=0) texto+="<br>Proceso finalizado"; else texto+="<br>"+datos[5];
            jQuery("#resultados").html(texto);
            mueve(datos[3]);
         }
      },
      complete: function() {
         if (datos[4]>0) setTimeout(progreso, 1000); 
         else {jQuery("#proceso").html("Procesar"); mueve(0);}
      }
   });
};

function mueve(width) {
   var elem = document.getElementById("rjc_barra"); 
   if (elem) {elem.style.width = width + "%";}
}