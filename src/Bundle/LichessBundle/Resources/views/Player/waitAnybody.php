<?php $view->extend('LichessBundle::layout') ?>
<?php $view['slots']->set('title', 'Lichess - '.$view['translator']->_('Play with anybody').' - '.$player->getColor()) ?>

<div class="lichess_game lichess_game_not_started waiting_opponent clearfix lichess_player_<?php echo $player->getColor() ?>">
    <div class="lichess_board_wrap">
        <?php $view->output('LichessBundle:Main:staticBoard', array('color' => $player->getColor())) ?>
        <div class="lichess_overboard wait_anybody">
            <img src="/bundle/lichess/images/hloader.gif" width="220" height="33" /><br />
            <?php echo $view['translator']->_('Waiting for opponent') ?>...
            <a class="invite_tools"><?php echo $view['translator']->_('Taking too long?') ?></a>
            <div class="invite_tools">
                <?php echo $view['translator']->_('Bring people to play with you') ?>:
                <a class="a2a_dd" href="http://www.addtoany.com/share_save?linkurl=http%3A%2F%2Flichess.org%2F&amp;linkname=Best%20web%20Chess%20game%20ever!"><img src="http://static.addtoany.com/buttons/share_save_171_16.png" width="171" height="16" alt="Share/Bookmark"/></a>
            </div>
        </div>
    </div> 
    <div class="lichess_ground">
        <div class="lichess_table lichess_table_not_started">
        <a href="<?php echo $view['router']->generate('lichess_invite_friend', array('color' => $player->getColor())) ?>" class="lichess_button" title="<?php echo $view['translator']->_('Invite a friend to play with you') ?>"><?php echo $view['translator']->_('Play with a friend') ?></a>
        <a href="<?php echo $view['router']->generate('lichess_invite_ai', array('color' => $player->getColor())) ?>" class="lichess_button" title="<?php echo $view['translator']->_('Challenge the artificial intelligence') ?>"><?php echo $view['translator']->_('Play with the machine') ?></a>
        <span class="lichess_button active"><?php echo $view['translator']->_('Play with anybody') ?></span>
        </div>
    </div>
</div>

<?php $view->output('LichessBundle:Game:data', array('player' => $player, 'possibleMoves' => null, 'parameters' => $parameters, 'isOpponentConnected' => false)) ?>
