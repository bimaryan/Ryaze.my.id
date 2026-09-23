<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resume - {{ $profile['name'] }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1, h2, h3, p, ul { margin: 0; padding: 0; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 24pt; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
        .header .contact-info { font-size: 10pt; color: #555; }
        
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14pt; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #ccc; margin-bottom: 10px; padding-bottom: 3px; color: #111; }
        
        .item { margin-bottom: 15px; }
        .item-header { width: 100%; margin-bottom: 3px; }
        .item-title { font-weight: bold; font-size: 12pt; }
        .item-subtitle { font-style: italic; color: #555; }
        .item-date { float: right; font-size: 10pt; color: #666; font-weight: bold; }
        .item-desc { margin-top: 5px; font-size: 10.5pt; }
        .item-desc ul { padding-left: 20px; margin-top: 5px; }
        .item-desc li { margin-bottom: 3px; }
        
        .skills-container { width: 100%; }
        .skill-group { margin-bottom: 10px; }
        .skill-group-name { font-weight: bold; width: 120px; display: inline-block; vertical-align: top; }
        .skill-list { display: inline-block; width: calc(100% - 130px); }
        
        .bio { font-size: 11pt; margin-bottom: 20px; text-align: justify; }
        
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $profile['name'] }}</h1>
        <div class="contact-info">
            {{ $profile['location'] }}
            @if($profile['email']) | {{ $profile['email'] }} @endif
            @if($profile['whatsapp']) | +62{{ ltrim($profile['whatsapp'], '0') }} @endif
            <br>
            @if($profile['linkedin']) LinkedIn: {{ str_replace(['https://', 'www.'], '', $profile['linkedin']) }} @endif
            @if($profile['github']) | GitHub: {{ str_replace(['https://', 'www.'], '', $profile['github']) }} @endif
        </div>
    </div>

    @if($profile['bio'])
    <div class="section bio">
        {!! strip_tags($profile['bio']) !!}
    </div>
    @endif

    @if($experiences->count() > 0)
    <div class="section">
        <div class="section-title">Professional Experience</div>
        @foreach($experiences as $exp)
            <div class="item">
                <div class="item-header">
                    <span class="item-title">{{ $exp->role }}</span>
                    <span class="item-date">{{ $exp->period }}</span>
                </div>
                <div class="item-subtitle">{{ $exp->company }}</div>
                <div class="item-desc">
                    {!! $exp->description !!}
                </div>
            </div>
        @endforeach
    </div>
    @endif

    @if($educations->count() > 0)
    <div class="section">
        <div class="section-title">Education</div>
        @foreach($educations as $edu)
            <div class="item">
                <div class="item-header">
                    <span class="item-title">{{ $edu->institution }}</span>
                    <span class="item-date">{{ $edu->period }}</span>
                </div>
                <div class="item-subtitle">{{ $edu->degree }}</div>
            </div>
        @endforeach
    </div>
    @endif

    @if($skillGroups->count() > 0)
    <div class="section">
        <div class="section-title">Technical Skills</div>
        <div class="skills-container">
            @foreach($skillGroups as $group)
                <div class="skill-group">
                    <span class="skill-group-name">{{ $group->label }}:</span>
                    <span class="skill-list">
                        {{ implode(', ', $group->skills->pluck('name')->toArray()) }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($portfolios->count() > 0)
    <div class="section">
        <div class="section-title">Projects & Portfolio</div>
        @foreach($portfolios as $port)
            <div class="item">
                <div class="item-header">
                    <span class="item-title">{{ $port->title }}</span>
                </div>
                @if(!empty($port->tags))
                <div class="item-subtitle">Tech Stack: {{ implode(', ', $port->tags) }}</div>
                @endif
                <div class="item-desc">
                    {{ strip_tags($port->description) }}
                </div>
            </div>
        @endforeach
    </div>
    @endif

</body>
</html>
