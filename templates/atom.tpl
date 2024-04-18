<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="/atomfeed.xsl" type="text/xsl"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <title>{$work->title}</title>
  <subtitle>{$work->title} の新着エピソード</subtitle>
  <link rel="self" href="{$config.site_top}/{$workId}" />
  <link rel="alternate" href="{$config.kakuyomu_top}/works/{$workId|escape:'url'}" type="text/html"/>
  <updated>{if $work->episodes}{$work->episodes[0]->date|atom_date_format}{else}{$smarty.now|atom_date_format}{/if}</updated>
  <generator>https://github.com/fuktommy/kakuyomu-rss</generator>
  <author><name>{$work->author}</name></author>
  <id>tag:fuktommy.com,2021:kakuyomu.rss</id>
  <icon>{$config.site_top}/favicon.ico</icon>
{foreach from=$work->episodes item=episode}
  <entry>
    <title>{$episode->title|htmlspecialchars_decode|mbtruncate:140}</title>
    <link rel="alternate" href="{$config.kakuyomu_top}{$episode->path}"/>
    <summary type="text">{$episode->title}</summary>
    <published>{$episode->date|atom_date_format}</published>
    <updated>{$episode->date|atom_date_format}</updated>
    <id>tag:fuktommy.com,2021:kakuyomu.rss/{$episode->id}</id>
  </entry>
{/foreach}
</feed>
