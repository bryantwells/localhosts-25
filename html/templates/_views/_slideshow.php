<slide-show class="Slideshow">

	<?php foreach ($entry->contents as $slide): ?>

		<figure class="Slide Slide--<?= $slide->type ?>">

			<!-- Text Slide -->
			<?php if ($slide->type == 'text'): ?>
				<?php $isRendering = isset($slide->meta->type) && $slide->meta->type == 'rendering'; ?>
				<div class="Slide-bodyText <?= ($isRendering) ? 'Slide-bodyText--rendering' : '' ?> ">
					<?= $slide->parsed ?>
				</div>
			<?php endif; ?>

			<!-- Image Slide -->
			<?php if ($slide->type == 'image'): ?>
				<img class="Slide-image" src="/<?= $slide->path ?>" alt="">
			<?php endif; ?>

			<!-- Video Slide -->
			<?php if ($slide->type == 'video'): ?>
				<video class="Slide-video" src="/<?= $slide->path ?>" controls loop muted>
				</video>
			<?php endif; ?>

			<!-- URL Slide -->
			<?php if ($slide->type == 'url'): ?>
				<iframe class="Slide-iframe" src="<?= $slide->url ?>" frameborder="0"></iframe>
			<?php endif; ?>

			<!-- PDF Slide -->
			<?php if ($slide->type == 'pdf'): ?>
				<object class="Slide-pdf" 
					data="/<?= $slide->path ?>" 
					type="application/pdf">
				</object>
			<?php endif; ?>

			<!-- Caption -->
			<?php if (isset($slide->meta->caption)): ?>
				<figcaption class="Slide-caption">
					<?= $slide->meta->caption ?>
				</figcaption>
			<?php endif; ?>

			<!-- Notes -->
			<?php if (isset($slide->meta->notes)): ?>
				<figcaption class="Slide-notes">
					<?php if (is_array($slide->meta->notes)): ?>
						<ul>
							<?php foreach ($slide->meta->notes as $note): ?>
								<li><?= $note ?></li>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<?= $slide->meta->notes ?>
					<?php endif; ?>
				</figcaption>
			<?php endif; ?>

		</figure>
		
	<?php endforeach; ?>

</slide-show>