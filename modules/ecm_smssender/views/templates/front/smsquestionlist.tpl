{extends file='page.tpl'}

{block name="page_content"}
    <div id="question_content">
        <h1>{l s='Page for evaluating the work of the store' mod='ecm_smssender'}</h1>

        <form action="{$action_path}" method="POST">
            <input type="hidden" name="id_order" value="{$id_order}">
            <br />
            <p>
                {assign var=items_question value=$arr_question}

                {$i = 1}
                {foreach $items_question as $v}
                    <b><input type="hidden" name="question[]" value="{$v['id_question']}">{$v['question_ru']}<br/></b>
                        {$b = 0}
                        {while $b <= 3}
                            <input type="radio" name="answer_{$v['id_question'] - 1}" value="{$arr_answer[$i][$b]['id_answer']}"> {$arr_answer[$i][$b++]['answer_ru']}<br />
                        {/while}
                        <input type="hidden" value="{$i++}">
                        <br />
                        <br />
                        {continue}
                {/foreach}
            </p>
            <p><button type="submit" value="1" id="submit_sms_answer_btn" name="submitSMSAnswer" class="bg-success border-0 col-md-1 pb-2 pt-2 text-align-center" style="color: #FFFFF0">{l s='Submit Answer' mod='ecm_smssender'}</button></p>
        </form>
    </div>

    <h1 id="h1_success" style="display: none;">Cпасибо за ответы!</h1>

    <script>
        document.querySelector('#submit_sms_answer_btn').onclick = function(){
            alert('Cпасибо за ответы!');
        };
    </script>
{/block}