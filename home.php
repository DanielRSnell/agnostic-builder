<?php
/**
 * The main template file
 *
 * @package YourThemeName
 */

get_header();

// Mock data for notes
$mock_notes = [
    1 => [
        'title' => "Game Day: Packers vs Bears",
        'date' => "September 10, 2023",
        'note' => "*Doors Open at 11:25 AM | Game at 2:25 PM",
        'content' => "Get ready for an epic showdown as the Packers face off against their long-time rivals, the Bears. Arrive early to soak in the pre-game atmosphere and secure your spot in the stands for this classic NFC North battle.",
    ],
    2 => [
        'title' => "Upcoming: Lions at Lambeau",
        'date' => "September 28, 2023",
        'note' => "*Doors Open at 5:30 PM | Game at 7:15 PM",
        'content' => "The Lions are coming to Lambeau Field for a primetime matchup under the lights. Don't miss this divisional clash as the Packers defend their home turf in what promises to be an electrifying evening of football.",
    ],
    3 => [
        'title' => "Rivalry Week: Vikings Showdown",
        'date' => "October 29, 2023",
        'note' => "*Doors Open at 10:00 AM | Kickoff at 12:00 PM",
        'content' => "It's rivalry week, and the Vikings are in town! This game is crucial for both teams' playoff aspirations. The energy at Lambeau will be off the charts, so make sure you're part of this unforgettable experience.",
    ],
];

// REPLACE THE ABOVE CODE WITH ACTUAL WORDPRESS QUERY:
// $query = new WP_Query([
//     'post_type' => 'game_notes',
//     'posts_per_page' => 3,
//     'orderby' => 'date',
//     'order' => 'DESC'
// ]);
//
// $mock_notes = [];
// if ($query->have_posts()) {
//     while ($query->have_posts()) {
//         $query->the_post();
//         $mock_notes[get_the_ID()] = [
//             'title' => get_the_title(),
//             'date' => get_the_date('F j, Y'),
//             'note' => get_field('note'),
//             'content' => get_field('content')
//         ];
//     }
//     wp_reset_postdata();
// }
?>

<div x-data="notesData">
    <!-- Top Toolbar -->
    <div class="p-4 shadow-md bg-base-300">
        <div class="container flex items-center justify-between mx-auto">
            <h1 class="text-2xl font-bold">Game Notes</h1>
            <button
                @click="toggleNotes"
                class="btn btn-primary"
            >
                <span x-text="showNotes ? 'Hide Notes' : 'Show Notes'"></span>
            </button>
        </div>
    </div>

    <!-- Notes Content -->
    <div class="container p-4 mx-auto">
        <div x-show="showNotes" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90">
            <div class="p-6 rounded-lg shadow-xl bg-base-200">
                <div x-ref="notesContainer">
                    <?php foreach ($mock_notes as $id => $note): ?>
                        <div x-ref="note-<?php echo $id; ?>" class="hidden">
                            <h2 class="mb-2 text-3xl font-bold"><?php echo esc_html($note['title']); ?></h2>
                            <h3 class="mb-4 text-xl"><?php echo esc_html($note['date']); ?></h3>
                            <p class="mb-4 text-lg font-bold"><?php echo esc_html($note['note']); ?></p>
                            <p class="mb-4 text-lg"><?php echo esc_html($note['content']); ?></p>
                        </div>
                    <?php endforeach;?>
                </div>
                <div class="flex justify-between mt-6">
                    <button @click="previousNote" class="btn btn-primary" :disabled="currentIndex === 0">Previous</button>
                    <button @click="nextNote" class="btn btn-primary" :disabled="currentIndex === <?php echo count($mock_notes) - 1; ?>">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notesData', () => ({
            showNotes: false,
            currentIndex: 0,
            noteIds: <?php echo json_encode(array_keys($mock_notes)); ?>,
            toggleNotes() {
                this.showNotes = !this.showNotes;
                if (this.showNotes) {
                    this.$nextTick(() => this.showCurrentNote());
                }
            },
            showCurrentNote() {
                this.noteIds.forEach((id, index) => {
                    const noteElement = this.$refs[`note-${id}`];
                    if (index === this.currentIndex) {
                        noteElement.classList.remove('hidden');
                    } else {
                        noteElement.classList.add('hidden');
                    }
                });
            },
            nextNote() {
                if (this.currentIndex < this.noteIds.length - 1) {
                    const oldNoteElement = this.$refs[`note-${this.noteIds[this.currentIndex]}`];
                    this.currentIndex++;
                    const newNoteElement = this.$refs[`note-${this.noteIds[this.currentIndex]}`];

                    this.transitionNotes(oldNoteElement, newNoteElement);
                }
            },
            previousNote() {
                if (this.currentIndex > 0) {
                    const oldNoteElement = this.$refs[`note-${this.noteIds[this.currentIndex]}`];
                    this.currentIndex--;
                    const newNoteElement = this.$refs[`note-${this.noteIds[this.currentIndex]}`];

                    this.transitionNotes(oldNoteElement, newNoteElement);
                }
            },
            transitionNotes(oldNote, newNote) {
                if ('startViewTransition' in document) {
                    document.startViewTransition(() => {
                        oldNote.classList.add('hidden');
                        newNote.classList.remove('hidden');
                    });
                } else {
                    oldNote.classList.add('hidden');
                    newNote.classList.remove('hidden');
                }
            }
        }));
    });
</script>

<style>

</style>

<?php
get_footer();
?>