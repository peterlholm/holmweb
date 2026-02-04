"view module for motion"
from datetime import datetime, timedelta, date
import json
from decimal import Decimal
from django.shortcuts import render
from django.http import JsonResponse, HttpResponse

from django.db.models import Avg
import django.utils

from .models import Weight, BloodPressure


def index(request):
    """Dashboard view showing weight trends and charts"""
    start_date_str = request.GET.get('startdate', None)

    if start_date_str:
        try:
            start_date = datetime.strptime(start_date_str, '%Y-%m-%d').date()
        except ValueError:
            start_date = date.today() - timedelta(days=180)
    else:
        start_date = date.today() - timedelta(days=180)

    # Get weight data for the chart
    weights = Weight.objects.filter(
        person_id=1,
        date__gte=start_date
    ).order_by('date')

    # Calculate weight change
    now = datetime.now()
    one_week = timedelta(days=7)
    two_weeks = timedelta(days=14)

    today = now.date()
    week1_start = today - two_weeks
    week1_end = today - one_week
    week2_start = today - one_week
    week2_end = today

    vagt1 = Weight.objects.filter(
        person_id=1,
        date__gt=week1_start,
        date__lte=week1_end
    ).aggregate(avg=Avg('weight'))['avg'] or 0

    vagt2 = Weight.objects.filter(
        person_id=1,
        date__gt=week2_start,
        date__lte=week2_end
    ).aggregate(avg=Avg('weight'))['avg'] or 0

    if vagt1 and vagt2:
        vagtdiff = round((vagt2 - vagt1) * 1000)
    else:
        vagtdiff = 0

    # Prepare data for charts
    weight_data = [{'x': str(w.date), 'y': float(w.weight)} for w in weights]
    bmi_data = [{'x': str(w.date), 'y': float(w.bmi)} for w in weights if w.bmi]
    fat_data = [{'x': str(w.date), 'y': float(w.fat)} for w in weights if w.fat]
    muscle_data = [{'x': str(w.date), 'y': float(w.muscle)} for w in weights if w.muscle]

    context = {
        'start_date': start_date.isoformat(),
        'weight_data': json.dumps(weight_data),
        'bmi_data': json.dumps(bmi_data),
        'fat_data': json.dumps(fat_data),
        'muscle_data': json.dumps(muscle_data),
        'vagtdiff': vagtdiff,
        'vagt1': round(float(vagt1), 1) if vagt1 else 0,
        'vagt2': round(float(vagt2), 1) if vagt2 else 0,
    }

    return render(request, 'motion/index.html', context)


def blood_pressure(request):
    """View for displaying blood pressure (blodtryk) data"""
    # Get all weight data for the main chart
    weights = Weight.objects.filter(person_id=1).order_by('date')
    blood_pressures = BloodPressure.objects.filter(person_id=1).order_by('date')

    # Prepare data for charts
    weight_data = [{'x': str(w.date), 'y': float(w.weight)} for w in weights]
    bmi_data = [{'x': str(w.date), 'y': float(w.bmi)} for w in weights if w.bmi]
    bp_data = [{'x': str(bp.date), 'systolic': bp.systolic, 'diastolic': bp.diastolic}
               for bp in blood_pressures]

    context = {
        'weight_data': json.dumps(weight_data),
        'bmi_data': json.dumps(bmi_data),
        'bp_data': json.dumps(bp_data),
    }

    return render(request, 'motion/blodtryk.html', context)


def save_weight(request):
    """View for saving weight data"""
    if request.method == 'GET':
        if not request.GET.get('DateTime'):
            # Show form
            now = datetime.now()
            context = {
                'datetime': now.strftime('%Y-%m-%dT%H:%M'),
                'date': now.strftime('%Y-%m-%d'),
            }
            return render(request, 'motion/save.html', context)

    if request.method == 'GET':
        datetime_str = request.GET.get('DateTime')
        date_str = request.GET.get('Date')
        age = request.GET.get('Age')
        height = request.GET.get('Height')
        weight = request.GET.get('Weight')
        fat = request.GET.get('Fat')
        bone = request.GET.get('Bone')
        muscle = request.GET.get('Muscle')
        vfat = request.GET.get('Vfat')
        moisture = request.GET.get('Moisture')
        calorie = request.GET.get('Calorie')
    else:
        # Extract form data
        datetime_str = request.POST.get('DateTime')
        date_str = request.POST.get('Date')
        age = request.POST.get('Age')
        height = request.POST.get('Height')
        weight = request.POST.get('Weight')
        fat = request.POST.get('Fat')
        bone = request.POST.get('Bone')
        muscle = request.POST.get('Muscle')
        vfat = request.POST.get('Vfat')
        moisture = request.POST.get('Moisture')
        calorie = request.POST.get('Calorie')

    try:
        # Parse datetime
        if datetime_str:
            dt = datetime.fromisoformat(datetime_str)
        else:
            dt = django.utils.timezone.now()

        if django.utils.timezone.is_naive(dt):
            dt = django.utils.timezone.make_aware(dt)

        # Create Weight record
        Weight.objects.create(
            person_id=1,
            date_time=dt,
            date=datetime.strptime(date_str, '%Y-%m-%d').date(),
            gender=1,
            age=int(age) if age else 0,
            height=Decimal(height) if height else None,
            weight=Decimal(weight) if weight else None,
            fat=Decimal(fat) if fat else None,
            bone=Decimal(bone) if bone else None,
            muscle=Decimal(muscle) if muscle else None,
            vfat=Decimal(vfat) if vfat else None,
            moisture=Decimal(moisture) if moisture else None,
            calorie=int(calorie) if calorie else None,
        )

        return JsonResponse({'success': True, 'message': 'Record saved successfully'})
    except (ValueError, TypeError) as e:
        return JsonResponse({'success': False, 'error': str(e)}, status=400)


def save_blood_pressure(request):
    """View for saving blood pressure data"""
    if request.method == 'POST':
        try:
            date_str = request.POST.get('Date')
            systolic = request.POST.get('Systolic')
            diastolic = request.POST.get('Diastolic')

            # Create Blood Pressure record
            BloodPressure.objects.create(
                person_id=1,
                date=datetime.strptime(date_str, '%Y-%m-%d').date(),
                systolic=int(systolic),
                diastolic=int(diastolic),
            )
            return JsonResponse({'success': True, 'message': 'Blood pressure record saved'})
        except (ValueError, TypeError) as e:
            return JsonResponse({'success': False, 'error': str(e)}, status=400)
    else:
        return HttpResponse("noget galt i save")
