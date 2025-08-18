<?php 
	$entry = $SITE->entries['01_home'];
	$calendar = $entry->meta->calendar;
	$currentDate = new DateTime("@{$entry->meta->current_date}");
?>

<main class="Page Page--home">
	<section class="Calendar">
		<?php foreach ($calendar as $calendarItem): ?>

			<?php 
				$date = new DateTime("@{$calendarItem['date']}");
				
				$past = $date <= $currentDate;
				$current = $date == $currentDate;
				$break = $date == new DateTime('2024-10-16') || 
						$date == new DateTime('2024-11-27');

				$agenda = array_key_exists('agenda', $calendarItem)
					? $calendarItem['agenda']
					: null;
				$notes = array_key_exists('notes', $calendarItem)
					? $calendarItem['notes']
					: null;
				$assignments = array_key_exists('assignments', $calendarItem)
					? $calendarItem['assignments']
					: null;
			?>

			<article class="Date<?= ($break) ? ' is-break' : '' ?><?= ($past) ? ' is-past' : '' ?><?= ($current) ? ' is-current' : '' ?>">
				
				<header class="Date-header">
					<h2><?= $date->format('n.j'); ?></h2>
				</header>

				<?php if ($agenda): ?>
					<section class="Date-section">
						<header class="Date-sectionHeader">
							<h3>Agenda</h3>
						</header>
						<ul class="Date-list">
							<?php foreach ($agenda as $agendaItem): ?>
								<li class="Date-item"><?= $agendaItem ?></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ($assignments && $past): ?>
					<section class="Date-section">
						<header class="Date-sectionHeader">
							<h3>Assignments</h3>
						</header>
						<ul class="Date-list">
							<?php foreach ($assignments as $assignment): ?>
								<li class="Date-item"><?= $assignment ?></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

			</article>

		<?php endforeach; ?>
	</section>
</main>
