<?php
/**
 * Template Name: Vendors
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
            <article id="clients_list" class="page type-page status-publish hentry" role="article">
                <header class="entry-header">
                    <h1 class="entry-title">Vendors</h1>
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <table id="client_listing" cellpadding="4" width="100%">
                        <tr>
                            <td><h3>Organization name</h3></td>
                            <td align="right"><h3>Phone</h3></td>
                        </tr>
                        <?php
                        $vendors = $cmsdb->get_results('select * from users where account_type=2 and organization_name <> "" order by organization_name');
                        foreach ($vendors as $vendor) :
                            ?>
                            <tr>
                                <td><a class="client-title" href="<?php echo $base_url . '/vendors?mode=view&cid=' . $vendor->id; ?>"><?php echo $vendor->organization_name; ?></a></td>
                                <td align="right"><?php echo $vendor->phone; ?></td>
                            </tr>
                        <?php endforeach; ?>         
                    </table>
            </article>
        <?php endif; ?>
        <?php if ($mode == 'view'): ?>
            <?php
            $vendor_id = $wp_query->get('cid') <> "" ? $wp_query->get('cid') : false;
            if (false === $vendor_id):
                echo 'Invalid Parameter';
            else:
                $vendor = $cmsdb->get_row("select v.* from users v where v.id=$vendor_id");
                if(!sizeof($vendor)){
                    die('Invalid Parameter given');
                }
                ?>
        <a href="<?php echo $base_url; ?>/vendors">Back to all Vendors</a>
        <br />
        <br />
        <h1><?php echo $vendor->organization_name; ?></h1>
        <table cellpadding="3">
            <tr><td class="caption">Phone</td><td class="client_prop"><?php echo $vendor->phone; ?></td></tr>
            <tr><td class="caption">Email</td><td class="client_prop"><?php echo $vendor->email; ?></td></tr>
            <tr><td class="caption">Website</td><td class="client_prop"><?php echo $vendor->website; ?></td></tr>
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