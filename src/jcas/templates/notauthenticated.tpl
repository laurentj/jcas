<p>
    Sorry, you cannot use this application:
    {if $error}you are not authenticated{else}you are not authorized to use this application{/if}.
</p>
{if $error}
{$error}
{else}
<p><a href="{jurl 'jcas~jcas:logout'}">Try with an other login</a>.</p>
{/if}