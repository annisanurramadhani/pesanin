window.previewImage = function(input, previewId, placeholderId = null)
{
    const preview = document.getElementById(previewId);

    if(input.files && input.files[0])
    {

        const reader = new FileReader();


        reader.onload = function(e)
        {

            preview.src = e.target.result;

            preview.classList.remove('hidden');


            if(placeholderId)
            {
                const placeholder = document.getElementById(placeholderId);

                if(placeholder)
                {
                    placeholder.classList.add('hidden');
                }
            }

        }


        reader.readAsDataURL(input.files[0]);

    }
}