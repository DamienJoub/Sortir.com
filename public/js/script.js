
$(function(){

    //Utilisation de la librairie tootip.js (utilise popper.js)
    $('[data-toggle="tooltip"]').tooltip();


    /**
     * Click lien suppression
     */
    $('.delete-item').on('click',
        function(e){
            //arrête progagation de l'evenement
            e.preventDefault();

            //Récupération du lien générer dans le bouton
            let link = $(this).attr('href');
            var title = $(this).attr('title');
            var tooltipTitle = $(this).data('original-title');
            var ville = document.getElementById("ville_list");


            //Modification du contenu de la fenêtre modale
            if(ville){
                $('.modal-body p').text("Si vous supprimez cette ville, vous supprimerez également les sorties et lieux associés !");
            }

            //A cause de la librairie tooltip.js
            if(title === '' && tooltipTitle !== ''){
                title = tooltipTitle;
            }

            //Modification du titre de la fenêtre modale
            $('#deleteModalLabel').empty().html(title);

            //Insertion d'un data-link dans la div d'id deleteModal
            $('#deleteModal').data('link', link).modal();

        }
    );


    /**
     * Click bouton delete
     */
    $('#delete-modal-btn').on('click', function(){

        //récupération du lien
        var link = $('#deleteModal').data('link');

        if(link == ''){
            //TODO : prevoir une erreur
           console.log('Aucun lien assigné');
        }
        else{
            console.log(link);

            //provoque la redirection vers le lien dans link
            document.location.href = link;

            //Cache la fenêtre modal
            $('#deleteModal').modal('hide');

        }

    });


    //Filtre par catégorie d'idée
    $('#filtre_categorie').on('change', function () {

        var cat = $(this).val();
        document.location.href = $(this).data('route') + "" + cat;
    });

});

$(".users").on("click", function() {
    console.log("ok");
    if(screen.width <= 750) {
        var ul = $(".gestion_user");
        if (ul.css("display") === "none") {
            ul.css("display", "block")
        } else {
            ul.css("display", "none");
        }
    }
});