<?php
/**
 * Template Name: Document Page
 * 
 * Template for displaying document-like content with a table of contents.
 *
 * @package alkooutlet
 */

get_header();
?>

<main class="document">
    <div class="container">
        <?php 
     
            basic_wp_breadcrumbs();
      
        ?>

        <?php while (have_posts()) : the_post(); ?>
            <h1 class="document__title"><?php the_title(); ?></h1>

            <div class="document__wrapper">
                <div class="document__sidebar">
                    <div class="document__toc">
                        <h2 class="document__toc-title">SPIS TREŚCI</h2>
                        <nav class="document__toc-nav">
                            <ul class="document__toc-list">
                                <?php
                                // Save the original content to extract headings for TOC
                                ob_start();
                                the_content();
                                $content = ob_get_clean();

                                libxml_use_internal_errors(true);
                                $dom = new DOMDocument();
                                $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
                                libxml_clear_errors();

                                $xpath = new DOMXPath($dom);
                                $headings = $xpath->query('//h2 | //h3 | //h4');

                                $toc = [];
                                $i = 0;

                                foreach ($headings as $heading) {
                                    $tag = $heading->tagName;
                                    $text = trim($heading->textContent);
                                    $id = 'section-' . ++$i;
                                    $heading->setAttribute('id', $id);
                                    
                                    // Create list item with appropriate class based on heading level
                                    $class = '';
                                    if ($tag === 'h3') {
                                        $class = 'document__toc-subitem';
                                    } elseif ($tag === 'h4') {
                                        $class = 'document__toc-subsubitem';
                                    }
                                    
                                    echo '<li class="document__toc-item ' . $class . '">';
                                    echo '<a href="#' . $id . '" class="document__toc-link">' . $text . '</a>';
                                    echo '</li>';
                                    
                                    $toc[] = [
                                        'tag' => $tag,
                                        'text' => $text,
                                        'id' => $id,
                                    ];
                                }
                                ?>
                            </ul>
                        </nav>
                    </div>
                </div>
                
                <div class="document__content">
                    <?php 
                    // Output the modified content with added IDs for headings
                    echo $dom->saveHTML($dom->getElementsByTagName('body')->item(0));
                    ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
