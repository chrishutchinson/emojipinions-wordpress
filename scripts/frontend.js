// Parent element
var mountNode = document.getElementById('emojipinions');

// React config
var HelloMessage = React.createClass({displayName: "HelloMessage",
  getData: function() {
    console.log('getting data');
  },

  componentDidMount: function() {
    this.getData();
  },
  
  render: function() {
    return React.createElement("div", null, "This is post number ", this.props.post);
  }
});

// React rendering
React.render(React.createElement(HelloMessage, {post: mountNode.dataset.post}), mountNode);