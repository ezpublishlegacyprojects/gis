{* Global Information Systems - Line view *}

<div class="content-view-line">
    <div class="class-gis float-break">

    <h2><a href={$node.url_alias|ezurl}>{$node.data_map.name.content|wash}</a></h2>

    {section show=$node.data_map.intro.content.is_empty|not}
    <div class="attribute-short">
        {attribute_view_gui attribute=$node.data_map.intro}
    </div>
    {/section}

    </div>
</div>