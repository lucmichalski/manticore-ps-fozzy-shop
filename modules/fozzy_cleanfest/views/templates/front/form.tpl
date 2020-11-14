{extends file='page.tpl'}

{block name="page_content"}
<div id="question_content">
        <h1>{l s='Регистрация чека' mod='fozzy_cleanfest'}</h1>
        <input id="errordate" name="errordate" type="hidden" value="{l s='Введена некорректная дата!' mod='fozzy_cleanfest'}">  
        <input id="datet" name="datet" type="hidden" value="{l s='ДД-ММ-ГГГГ' mod='fozzy_cleanfest'}">         
        {if $return}
          {if $return.errorcode == 1}
          <div class="alert alert-warning">{$return.errormes}</div>
          {else}
          <div class="alert alert-success">{$return.errormes}</div>
          {/if}
        
        {else}
        <form action="{$action_path}" method="POST">
            <fieldset>
            <!-- Text input-->
            <div class="form-group">
              <label class="col-sm-12 control-label" for="firstname">{l s='Имя' mod='fozzy_cleanfest'}</label>  
              <div class="col-sm-12">
              <input id="firstname" name="firstname" type="text" class="form-control input-md" required oninvalid="this.setCustomValidity('{l s="Заполните это поле!" mod="fozzy_cleanfest"}')"
    oninput="setCustomValidity('')" title="{l s='Заполните это поле!' mod='fozzy_cleanfest'}">
              </div>
            </div>
            <!-- Text input-->
            <div class="form-group">
              <label class="col-sm-12 control-label" for="lastname">{l s='Фамилия' mod='fozzy_cleanfest'}</label>  
              <div class="col-sm-12">
              <input id="lastname" name="lastname" type="text" class="form-control input-md" required oninvalid="this.setCustomValidity('{l s="Заполните это поле!" mod="fozzy_cleanfest"}')"
    oninput="setCustomValidity('')" title="{l s='Заполните это поле!' mod='fozzy_cleanfest'}">
                
              </div>
            </div>
            <!-- Text input-->
            <div class="form-group">
              <label class="col-sm-12 control-label" for="phone">{l s='Телефон' mod='fozzy_cleanfest'}</label>  
              <div class="col-sm-12">
              <input id="phone" name="phone" type="text" class="form-control input-md" required oninvalid="this.setCustomValidity('{l s="Заполните это поле!" mod="fozzy_cleanfest"}')"
    oninput="setCustomValidity('')" title="{l s='Заполните это поле!' mod='fozzy_cleanfest'}">
                
              </div>
            </div>
            <!-- Text input-->
            <div class="form-group">
              <label class="col-sm-12 control-label" for="email">{l s='E-mail' mod='fozzy_cleanfest'}</label>  
              <div class="col-sm-12">
              <input id="email" name="email" type="text" class="form-control input-md" required oninvalid="this.setCustomValidity('{l s="Заполните это поле!" mod="fozzy_cleanfest"}')"
    oninput="setCustomValidity('')" title="{l s='Заполните это поле!' mod='fozzy_cleanfest'}">
                
              </div>
            </div>
            <!-- Text input-->
            <div class="form-group">
              <label class="col-sm-12 control-label" for="fiskal_mun">{l s='Номер чека' mod='fozzy_cleanfest'}</label>  
              <div class="col-sm-12">
              <input id="fiskal_num" name="fiskal_num" type="text" class="form-control input-md" required oninvalid="this.setCustomValidity('{l s="Заполните это поле!" mod="fozzy_cleanfest"}')"
    oninput="setCustomValidity('')" title="{l s='Заполните это поле!' mod='fozzy_cleanfest'}">
              </div>
            </div>
            <!-- Text input-->
            <div class="form-group required">
              <label class="col-sm-12 control-label" for="fiskal_date">{l s='Дата покупки' mod='fozzy_cleanfest'}</label>  
              <div class="col-sm-12">
              <input id="fiskal_date" name="fiskal_date" type="text" class="form-control input-md" required oninvalid="this.setCustomValidity('{l s="Заполните это поле!" mod="fozzy_cleanfest"}')"
    oninput="setCustomValidity('')" title="{l s='Заполните это поле!' mod='fozzy_cleanfest'}">
              </div>
            </div>
            <!-- Multiple Checkboxes (inline) -->
            <div class="form-group required">
              <label class="col-sm-12 control-label" for="pravila"></label>
              <div class="col-sm-12">
                <label class="checkbox-inline" for="pravila-0">
                  <input type="checkbox" required name="pravila" id="pravila-0"  oninvalid="this.setCustomValidity('{l s="Заполните это поле!" mod="fozzy_cleanfest"}')"
    oninput="setCustomValidity('')" title="{l s='Заполните это поле!' mod='fozzy_cleanfest'}">
                  {l s='Соглашаюсь на использование моих данных' mod='fozzy_cleanfest'}
                </label>
              </div>
            </div>
            <!-- Button -->
            <div class="form-group">
              <label class="col-sm-12 control-label" for="singlebutton"></label>
              <div class="col-sm-12">
                <button id="reg_button" name="reg_button" class="btn btn-primary" type="submit">{l s='Зарегестрировать чек' mod='fozzy_cleanfest'}</button>
              </div>
            </div>
            
            </fieldset>
  </form>
  {/if}
</div>
<div id="marketing">
<img src="modules/fozzy_cleanfest/views/img/fz_clear.jpg" />
</div>
{/block}