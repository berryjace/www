<?php
/**
 * Template Name: Clients
 */
get_header();
include_once(ABSPATH . '/cmsdb.php');
$cmsdb = CMS_DB::getInstance();

global $wp_query;

$base_url = get_bloginfo('url');
$mode = $wp_query->get('mode') <> "" ? $wp_query->get('mode') : 'list';


?>
<div id="primary">
    <div id="content">
        <?php if ($mode == 'list'): ?>
            <header class="entry-header">
                <h1 class="entry-title">Clients</h1>
            </header><!-- .entry-header -->

            <div class="entry-content">
                <table id="client_listing" cellpadding="4" width="100%">
                    <tr>
                        <td><h3>Organization name</h3></td>
                        <td align="right"><h3>Phone</h3></td>
                    </tr>
                    <?php
                    $clients = $cmsdb->get_results('select * from users where account_type=3 and organization_name <> "" order by organization_name');
                    foreach ($clients as $client) :
                        ?>
                        <tr>
                            <td><a class="client-title" href="<?php echo $base_url . '/clients?mode=view&cid=' . $client->id; ?>"><?php echo $client->organization_name; ?></a></td>
                            <td align="right"><?php echo $client->phone; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            <?php endif; ?>
            <?php if ($mode == 'view'): ?>
                <?php
                $client_id = $wp_query->get('cid') <> "" ? $wp_query->get('cid') : false;
                if (false === $client_id):
                    echo 'Invalid Parameter';
                else:
                    $client = $cmsdb->get_row("select c.*,cp.* from users c left join client_profiles cp on c.id=cp.user_id where c.id=$client_id");
                    if (!sizeof($client)) {
                        die('Invalid Parameter given');
                    }
                    ?>
                    <a href="<?php echo $base_url; ?>/clients">Back to all Clients</a>
                    <br />
                    <br />
                    <h1><?php echo $client->organization_name; ?></h1>
                    <table cellpadding="3">
                        <tr><td class="caption">Founded</td><td class="client_prop"><?php echo $client->org_founding_year; ?></td></tr>
                        <tr><td class="caption">Year</td><td class="client_prop"><?php echo date("Y",strtotime($client->greek_founding_year)); ?> </td></tr>
                        <tr><td class="caption">Number of collegiate chapters</td><td class="client_prop"><?php echo $client->greek_number_of_colg_chapters; ?></td></tr>
                        <tr><td class="caption">Number of alumni/ae chapters</td><td class="client_prop"><?php echo $client->greek_number_of_alumni_chapters; ?></td></tr>
                        <tr><td class="caption">Total number of undergraduates</td><td class="client_prop"><?php echo $client->greek_number_of_undergrads; ?></td></tr>
                        <tr><td class="caption">Total number of alumni/ae</td><td class="client_prop"><?php echo $client->greek_number_of_alumni; ?></td></tr>
                        <tr><td class="caption">Headquarters</td><td class="client_prop"><?php echo $client->headquarters; ?></td></tr>
                        <tr><td class="caption">Phone</td><td class="client_prop"><?php echo $client->phone; ?></td></tr>
                        <tr><td class="caption">Email</td><td class="client_prop"><?php echo $client->email; ?></td></tr>
                        <tr><td class="caption">Website</td><td class="client_prop"><?php echo $client->website; ?></td></tr>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <br />
    </div>

    <?php get_sidebar(); ?>
    <?php get_footer(); ?>

    <style type="text/css">
        .client-title{
            font-size:120%;
        }
        #client_listing td{
            border-bottom: 1px dotted #999;
            padding:9px 0 9px 0;
        }
        .caption{
            text-align: right;
            font-size:120%;
            font-weight: bold;
            padding:10px 10px 0 0;
        }
        .client_prop{
            font-size:100%;
            padding:10px 0 0 0;
        }
    </style>