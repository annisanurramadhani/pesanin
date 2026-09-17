/*
|--------------------------------------------------------------------------
| Merchant Finance JS
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| BANK ACCOUNT MODAL
|--------------------------------------------------------------------------
*/


function openBankModal() {


    const modal = document.getElementById(
        'bankModal'
    );


    if(modal){

        modal.classList.remove(
            'hidden'
        );

    }


}



function closeBankModal() {


    const modal = document.getElementById(
        'bankModal'
    );


    if(modal){

        modal.classList.add(
            'hidden'
        );

    }


}





/*
|--------------------------------------------------------------------------
| WITHDRAW MODAL
|--------------------------------------------------------------------------
*/


function openWithdrawModal() {


    const modal = document.getElementById(
        'withdrawModal'
    );


    if(modal){

        modal.classList.remove(
            'hidden'
        );

    }


}



function closeWithdrawModal() {


    const modal = document.getElementById(
        'withdrawModal'
    );


    if(modal){

        modal.classList.add(
            'hidden'
        );

    }


}





/*
|--------------------------------------------------------------------------
| CLOSE CLICK OUTSIDE MODAL
|--------------------------------------------------------------------------
*/


document.addEventListener(
    "DOMContentLoaded",
    function(){



        const bankModal =
            document.getElementById(
                'bankModal'
            );



        const withdrawModal =
            document.getElementById(
                'withdrawModal'
            );





        if(bankModal){


            bankModal.addEventListener(
                'click',
                function(e){


                    if(
                        e.target === bankModal
                    ){

                        closeBankModal();

                    }


                }
            );


        }






        if(withdrawModal){


            withdrawModal.addEventListener(
                'click',
                function(e){


                    if(
                        e.target === withdrawModal
                    ){

                        closeWithdrawModal();

                    }


                }
            );


        }





    }
);







/*
|--------------------------------------------------------------------------
| CLOSE WITH ESC BUTTON
|--------------------------------------------------------------------------
*/


document.addEventListener(
    "keydown",
    function(e){


        if(
            e.key === "Escape"
        ){


            closeBankModal();

            closeWithdrawModal();


        }


    }
);