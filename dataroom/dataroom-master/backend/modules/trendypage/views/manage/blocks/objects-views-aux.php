<script id="text-block-view" type="text/mustache">
    <div class="tp-content-block text-block {{blockSettings.classList}}">
        {{&content.text}}
    </div>
</script>

<script id="icon-list-view" type="text/mustache">
    <div class="tp-content-block icons-list {{blockSettings.classList}}">
        <ul>
            {{#each content.listItems}}
            <li>
                <span class="icon fa {{content.icon}}"></span>
                <h4 class="title" style="color:{{content.titleColor}};">
                    {{#if content.isLink}}
                    <a href="{{content.href}}">{{content.title}}</a>
                    {{else}}
                    {{content.title}}
                    {{/if}}
                </h4>
                {{#if content.text}}
                <div class="text">{{&content.text}}</div>
                {{/if}}
            </li>
            {{/each}}
        </ul>
    </div>
</script>

<script id="icon-list-item-view" type="text/mustache">
    <span>Icon List Item</span>
</script>

<script id="separator-view" type="text/mustache">
    <div class="tp-content-block separator {{blockSettings.classList}}">
        {{#is content.type 'line'}}
        <hr style="background-color: {{content.color}}; height: {{content.height}}px;" />
        {{else}}
        <div class="space" style="margin-bottom: {{content.height}}px;"></div>
        {{/if}}
    </div>
</script>

<script id="icon-box-view" type="text/mustache">
    <div class="tp-content-block icon-box {{blockSettings.classList}}">
        {{^is content.iconPosition 'right'}}
        <span class="icon fa {{content.icon}}"></span>
        {{/if}}
        <h4 class="title" style="color:{{content.titleColor}}">
            {{#if content.isLink}}
            <a href="{{content.href}}">{{content.title}}</a>
            {{else}}
            {{content.title}}
            {{/if}}
        </h4>
        {{#is content.iconPosition 'right'}}
        <span class="icon fa {{content.icon}}"></span>
        {{/if}}
        {{#if content.text}}
        <div class="text">{{&content.text}}</div>
        {{/if}}
    </div>
</script>

<script id="button-view" type="text/mustache">
    <div class="tp-content-block button {{blockSettings.classList}}" style="text-align: {{content.alignment}};">
        <a class="btn btn-info btn-{{content.size}}" href="{{content.href}}" {{#if content.targetBlank}}target="blank"{{/if}}
            style="color: {{content.titleColor}}; background-color: {{content.buttonColor}}; border-color: {{content.buttonColor}}!important;" >
            <span class="link-icon fa {{content.icon}}"></span>
            <span class="link-title">{{content.title}}</span>
        </a>
    </div>
</script>

<script id="table-view" type="text/mustache">
    <div class="tp-content-block table {{blockSettings.classList}}">
        <table class="table table-striped">
            <tbody>
                {{#each content.rowsData}}
                <tr class="{{meta.stylesClasses}}">
                    {{#each cols}}
                    <td class="{{meta.stylesClasses}}">
                        {{&text}}
                    </td>
                    {{/each}}
                </tr>
                {{/each}}
            </tbody>
        </table>
    </div>
</script>

<script id="image-view" type="text/mustache">
    <div class="tp-content-block image {{blockSettings.classList}}" style="text-align: {{content.alignment}};">
        {{#if content.isLink}}
        <a href="{{content.href}}" {{#if content.linkTargetBlank}}target="_blank"{{/if}}>
            <img src="{{content.src}}" alt="{{content.alt}}" />
        </a>
        {{else}}
        <img src="{{content.src}}" class="width-{{content.width}}" alt="{{content.alt}}"
            style="{{#is content.width 'custom'}}
                    width: {{content.width}};
                {{/if}}" />
        {{/if}}
    </div>
</script>

<script id="video-view" type="text/mustache">
    <div class="tp-content-block video {{blockSettings.classList}}">
        {{&content.youtubeCode}}
    </div>
</script>

<script id="slideshow-view" type="text/mustache">
    <div id="carousel-{{id}}" class="carousel slide {{blockSettings.classList}}" data-interval="{{getSlideDuration}}" data-ride="carousel">
        <ol class="carousel-indicators">
            {{#each content.images}}
            <li data-target="#carousel-{{id}}" data-slide-to="{{@index}}" class="{{#is @index 0}}active{{/if}}"></li>
            {{/each}}
        </ol>

        <div class="carousel-inner" role="listbox">
            {{#each content.images}}
            <div class="item{{#is @index 0}} active{{/if}}">
                <img src="{{src}}">
                {{#if hasCaption}}
                <div class="carousel-caption">
                    {{{caption}}}
                </div>
                {{/if}}
            </div>
            {{/each}}
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#carousel-{{id}}" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#carousel-{{id}}" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</script>

<script id="team-member-view" type="text/mustache">
    <div class="tp-content-block team-member {{blockSettings.classList}}">
        {{#if content.image}}
        <div class="image-container">
            <img class="image" src="{{content.image}}" alt="{{content.name}}">
        </div>
        {{/if}}
        <h3 class="name">{{content.name}}</h3>
        <a class="overlay" href="{{content.partnerLink}}">
            <div class="name">{{content.name}}</div>
            <div class="divider"></div>
            <div class="job-title">{{&content.jobTitle}}</div>
            {{#if content.installationDate}}
            <div class="date">installé en {{content.installationDate}}</div>
            {{/if}}

            <div class="divider"></div>
            <div class="email">{{content.email}}</div>
            <div class="phone">{{content.phoneNumber}}</div>
        </div>
    </div>
</script>

<script id="dynamic-block-view" type="text/mustache">
    <dynamic-block-{{content.blockId}}></dynamic-block-{{content.blockId}}>
</script>

<script id="header-view" type="text/mustache">
    <div class="tp-content-block header hidden">
        <img class="bg-image" src="{{content.src}}">
        <p class="heading-text">{{&content.title}}</p>
    </div>
</script>