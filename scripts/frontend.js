// Parent element
var mountNode = document.getElementById('emojipinions');

// React config
var Emojipinions = React.createClass({

	ajaxIsRunning: 0,

	getInitialState: function() {
		return {
			emoji: [],
			title: wpConfig.emojipinionsTitle
		};
	},

	_vote: function( key, cb ) {
		jQuery.ajax({
			url: wpConfig.adminAjax,
			type: 'post',
			data: {
				action: 'emojipinionsVote',
				post: wpConfig.postId,
				emoji: key.toString()
			},
			success: function( res ) {
				if( !res.success ) {
					console.error( res );
					return;
				}

				// Run the callback
				cb( res, key );
			}.bind(this),
			error: function( err ) {
				console.error( err );
			}
		});
	},

	_voteHandler: function( ev ) {
		var target = ev.target;
		var emojiStateClone = this.state.emoji;

		emojiStateClone[target.dataset.emoji].count = parseInt(this.state.emoji[target.dataset.emoji].count)+1;
		this.setState({
			emoji: emojiStateClone
		});

		this.ajaxIsRunning++;

		target.blur();

		this._vote(target.dataset.emoji, function( res, key ) {
			this.ajaxIsRunning--;

			if(this.ajaxIsRunning < 1) {
				var emojiStateClone = this.state.emoji;
				emojiStateClone[key].count = res.data;
				this.setState({
					emoji: emojiStateClone
				});
			}
		}.bind(this));
	},

	componentDidMount: function() {
		this.setState({
			emoji: wpConfig.emoji.meta,
			count: wpConfig.emoji.count
		});
	},
	
	render: function() {
		// Setup our emoji
		var emojis = [];
		for(var x = 0; x < this.state.count; x++) {
			var el = (
				<li key={x}>
					<span dangerouslySetInnerHTML={{__html: this.state.emoji[x].emoji}}></span>
					<button data-emoji={x} onClick={this._voteHandler}>{this.state.emoji[x].count}</button>
				</li>
				);

			emojis.push(el);
		}

		if(emojis.length > 0) {
			return (
				<div className="emojipinions__wrapper">
					<h3>{this.state.title}</h3>
					<ul className="emojipinions__list">{emojis}</ul>
				</div>
			);	
		}

		return (<div></div>);
	}

});

// React rendering
React.render(<Emojipinions />, mountNode);