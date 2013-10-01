{if $error}
<p class="error">{$error|eschtml}</p>
{/if}

<p>
    <label for="host">Host</label>
    {if $listhosts}
    <select name="host" id="host" >
        {foreach $listhosts as $h}
        <option {if $h == $host}selected="selected"{/if} value="{$h|eschtml}">{$h|eschtml}</option>
        {/foreach}
    </select>
    {else}
    <input  type="text" name="host" id="host" value="{$host|eschtml}">
    {/if}
</p>

<p>
    <label for="port">Port</label>
    <input type="text" name="port" id="port" value="{$port|eschtml}">
</p>

<p>
    <label for="context">Context</label>
    <input type="text" name="context" id="context" value="{$context|eschtml}">
</p>

<!--    <input type="radio" name="automatic_registering" id="ar_yes"> <label for="ar_yes">New user are automatically registered</label><br>
    <input type="radio" name="automatic_registering" id="ar_no"> <label for="ar_no">Only users registered by the administrator are allowed</label><br>-->
<fieldset>
    <legend>Accés des utilisateurs authentifiés</legend>
    <input type="radio" name="ar" id="ar_yes" {if $ar=='on'}checked="checked"{/if} value="on">
        <label for="ar_yes">Tous les utilisateurs</label><br>
    <input type="radio" name="ar" id="ar_no" {if $ar=='off'}checked="checked"{/if} value="off">
        <label for="ar_no">Uniquement les utilisateurs selectionnés par l'administrateur</label>
</fieldset>

<fieldset>
    <legend>Administrateur</legend>
    <p>
        <label for="login">login</label>
        <input type="text" name="login" id="login" value="{$login|eschtml}">
    </p>
    <p>
        <label for="email">email</label>
        <input type="text" name="email" id="email" value="{$email|eschtml}">
    </p>
</fieldset>
