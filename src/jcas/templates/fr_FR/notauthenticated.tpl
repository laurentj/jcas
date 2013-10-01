<p>
    Désolé, vous ne pouvez pas accéder à l'application :
    {if $error}vous n'êtes pas authentifié {else} vous n'en n'avez pas les droits{/if}.
</p>
{if $error}
{$error}
{else}
<p><a href="{jurl 'jcas~jcas:logout'}">Essayer avec un autre identifiant</a>.</p>
{/if}