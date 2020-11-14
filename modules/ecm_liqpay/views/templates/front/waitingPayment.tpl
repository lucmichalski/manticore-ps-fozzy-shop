{extends 'page.tpl'}
{block name='page_content'}
{block name='page_title'}
<h2>{l s='Waiting for payment' mod='ecm_liqpay'}</h2>
{/block}
<p>{l s='At the moment of payment is not received. Once it is received you will be able to see your order in your account' mod='ecm_liqpay'}</p>
<p>{l s='If you do not receive notification of payment please send his number' mod='ecm_liqpay'} <strong>{$ordernumber}</strong> <a href="{$link->getPageLink('contact-form.php', true)}">{l s='to support services' mod='ecm_liqpay'}</a></p>
{/block}