<style>
    .bottom-text-choice{
        position: absolute;
        bottom: 0;
        margin-left: auto;
        margin-right: auto;
        left: 0;
        right: 0;
        text-align: center;
        color: White;
        font-weight: bold;
        background-color: #25b9d7;
        font-size: 15px;
        padding: 5px;
    }
    .option-label{
        position: relative;
        height: 150px;
    }

    .option-label img{
        height: 100%;
        object-fit: cover;
    }
    /* HIDE RADIO */
    [type=radio] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* IMAGE STYLES */
    [type=radio] + img,  [type=radio]:checked + .no-selection{
        cursor: pointer;
    }

    /* CHECKED STYLES */
    [type=radio]:checked + img, [type=radio]:checked + .no-selection {
        border-top: 4px solid #25b9d7!important;
        padding-bottom: 4px;
    }

    .no-selection{
        width: 150px;
        height: 150px;
    }

</style>
<div class="form-group">
    <h2>ThreeLIB Modeles 3D</h2>
    <div>
        <div>
            {if $threelib_elements}

                <label for="noChoice">
                    <input class="input-hidden" class="input-hidden" id="noChoice" type="radio" name="threelib" value="" {if is_null($threelib) || $threelib === ''}checked{/if}>
                    <img class="img-fluid"
                         src="/modules/threelib/views/media/noSelection.png"
                         alt="nothing" />
                </label>


                {foreach from=$threelib_elements item=threelib_element key=i}

                    <label class="option-label" for="models{$threelib_element['id']}">
                        <div class="bottom-text-choice">{$threelib_element['title']}</div>

                        <input class="input-hidden" id="models{$threelib_element['id']}" type="radio" name="threelib" value="{$threelib_element['file']}" {if $threelib_element['file'] === $threelib}checked{/if}>

                        <img class="img-fluid"
                             src="{if $threelib_element['preview']}{$threelib_element['preview']}{else}https://via.placeholder.com/150{/if}"
                             alt="{$threelib_element['description']}" />

                    </label>
                {/foreach}
            {else}
                <p>Aucun modele 3D trouvé...</p>
            {/if}


            </input>
        </div>
</div>